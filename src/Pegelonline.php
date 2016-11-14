<?php
/**
 * @author Michael Pfister <michael@mp-development.de>
 * @copyright (c) 2016, Michael Pfister
 * @license MIT
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF
 * ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * https://opensource.org/licenses/MIT
 */

namespace Pfitzer\Pegelonline;

use OtherCode\Rest;

class Pegelonline
{

    /**
     * @var string
     */
    private $apiUrl = "https://www.pegelonline.wsv.de/webservices/rest-api/";

    /**
     * @var null|Rest\Rest
     */
    public $rest = null;

    /**
     * @var string
     */
    private $apiVersion = "v2";

    /**
     * @param Rest\Rest $rest
     */
    public function __construct(Rest\Rest $rest = null)
    {
        if ($rest === null) {
            $rest = new Rest\Rest();
        }
        $this->rest = $rest;
    }

    /**
     * get the URI to call the API
     *
     * @return string
     */
    public function getApiUrl()
    {
        return sprintf($this->apiUrl . '%s/', $this->apiVersion);
    }

    /**
     * @param bool $timeSeries
     * @param bool $currentMeasurement
     * @param bool $characteristicValues
     * @return mixed
     */
    public function getStations($timeSeries = false, $currentMeasurement = false, $characteristicValues = false)
    {
        $uriString = $this->getApiUrl()
            . 'stations.json?includeTimeseries=%s&includeCurrentMeasurement=%s&includeCharacteristicValues=%s';
        $url = sprintf(
            $uriString,
            $this->toString($timeSeries),
            $this->toString($currentMeasurement),
            $this->toString($characteristicValues)
        );

        return $this->getApiCall($url);
    }

    /**
     * @param $station name of the station
     * @return mixed
     */
    public function getStationByName($station) {
        $url = sprintf($this->getApiUrl() . 'stations/' . mb_strtoupper($station) . '.json');

        return $this->getApiCall($url);
    }

    /**
     * get measurements for a single station
     * if no start and end date given, it will return data of the last 10 days
     *
     * @param $station
     * @param string $from timestamp, ISO_8601
     * @param string $to timestamp, ISO_8601
     * @return mixed
     * @throws \Exception
     */
    public function getMeasurementsForStation($station, $from=false, $to=false)
    {
        $url = sprintf(
            $this->getApiUrl() . 'stations/%s/W/measurements.json',
            mb_strtoupper($station)
        );

        if(is_string($from) && is_string($to)) {
            $url = $url . sprintf('?start=%s&end=%s', $from, $to);
        }

        return $this->getApiCall($url);
    }

    /**
     * @param bool $includeStations
     * @return mixed
     * @throws \Exception
     */
    public function getWaters($includeStations=false)
    {
        $url = $this->getApiUrl() . sprintf('waters.json?includeStations=%s', $this->toString($includeStations));

        return $this->getApiCall($url);
    }

    /**
     * @param $url
     * @param null $body
     * @return mixed
     * @throws \Exception
     */
    protected function getApiCall($url, $body = null)
    {

        $val = $this->rest->get($url, $body);

        switch ($val->code) {
            case 400:
            case 404:
                $ret = json_decode($val->body);
                throw new \InvalidArgumentException($ret->message);
                break;
            case 200:
            case 201:
                return json_decode($val->body);
                break;
            default:
            case 0:
                throw new \Exception($val->error->message);
                break;
        }
    }

    /**
     * @param bool $arg
     * @return string
     */
    protected function toString($arg)
    {
        if ($arg) {
            return 'true';
        }

        return 'false';
    }
}