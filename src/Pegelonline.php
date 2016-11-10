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

        return $this->get($url);
    }

    /**
     * @param $name der name der Station
     * @return mixed
     */
    public function getStationByName($name) {
        $url = sprintf($this->getApiUrl() . '/stations/' . mb_strtoupper($name) . '.json');

        return $this->get($url);
    }

    /**
     * @param $url
     * @param null $body
     * @return mixed
     */
    private function get($url, $body = null)
    {

        $val = $this->rest->get(urlencode($url), $body);

        switch ($val->code) {
            case 200:
            case 201:
                return json_decode($val->body);
                break;
        }
    }

    /**
     * @param bool $arg
     * @return string
     */
    private function toString($arg)
    {
        if ($arg) {
            return 'true';
        }

        return 'false';
    }
}