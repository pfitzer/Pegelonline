<?php

namespace Pfitzer\Pegelonline;

use OtherCode\Rest;

/**
 * Class Pegelonline
 *
 * @author Michael Pfister <michael@mp-development.de>
 * @copyright (c) 2016, Michael Pfister
 * @license MIT
 * @package Pfitzer\Pegelonline
 * @see https://www.pegelonline.wsv.de Documentation of the Api
 */
class Pegelonline
{

    /**
     * url to the Pegelonline Api
     *
     * @var string
     */
    private $apiUrl = "https://www.pegelonline.wsv.de/webservices/rest-api/";

    /**
     * the Rest Object
     *
     * @var Rest\Rest
     */
    public $rest = null;

    /**
     * which api version to use
     *
     * @var string
     */
    private $apiVersion = "v2";

    /**
     * the constructor
     *
     * @param Rest\Rest $rest we inject the Rest\Rest object so we can mock it in unit testing
     */
    public function __construct(Rest\Rest $rest = null)
    {
        if ($rest === null) {
            $rest = new Rest\Rest();
        }
        $this->rest = $rest;
    }

    /**
     * get a list of all stations
     *
     * @param bool $timeSeries inlcude avaiable timeseries
     * @param bool $currentMeasurement inlcude current measurement
     * @param bool $characteristicValues inlcude characteristic values
     * @return \stdClass
     * @throws \InvalidArgumentException
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
     * get info about a single station
     *
     * @param $station name of the station
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public function getStationByName($station)
    {
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
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public function getMeasurementsForStation($station, $from = false, $to = false)
    {
        $url = sprintf(
            $this->getApiUrl() . 'stations/%s/W/measurements.json',
            mb_strtoupper($station)
        );

        if (is_string($from) && is_string($to)) {
            $url = $url . sprintf('?start=%s&end=%s', $from, $to);
        }

        return $this->getApiCall($url);
    }

    /**
     * get all waters
     *
     * @param bool $includeStations includes a list of avaiable stations for each water
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public function getWaters($includeStations = false)
    {
        $url = $this->getApiUrl() . sprintf('waters.json?includeStations=%s', $this->toString($includeStations));

        return $this->getApiCall($url);
    }

    /**
     * get the water gauge timeline for a station including current measurement
     *
     * @param $station
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public function getTimelineForStation($station)
    {
        $url = $this->getApiUrl() . sprintf('/stations/%s/W.json?includeCurrentMeasurement=true', mb_strtoupper($station));

        return $this->getApiCall($url);
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
     * calls the rest api and handle the return values
     *
     * @uses Rest\Rest to make the Api call
     *
     * @param string $url
     * @param null $body
     * @return \stdClass
     * @throws \InvalidArgumentException
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
                throw new \InvalidArgumentException($val->error->message);
        }
    }

    /**
     * converts boolean to string
     *
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