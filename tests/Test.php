<?php

namespace Pfitzer\Pegelonline;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit_Framework_TestCase;
use Pfitzer\Pegelonline;
use OtherCode\Rest;

/**
 * Class PegelonlineTest
 *
 * @author Michael Pfister <michael@mp-development.de>
 * @copyright (c) 2016, Michael Pfister
 * @license MIT
 * @package Pfitzer\Pegelonline
 */
class PegelonlineTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->restMock = $this->getMockBuilder(Rest\Rest::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstruct()
    {

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $this->assertInstanceOf(Rest\Rest::class, $pgOnline->rest);

        $pgOnline2 = new Pegelonline\Pegelonline();
        $this->assertInstanceOf(Rest\Rest::class, $pgOnline2->rest);
    }

    public function testApiUrl()
    {
        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $this->assertEquals('https://www.pegelonline.wsv.de/webservices/rest-api/v2/', $pgOnline->getApiUrl());
    }

    public function testGetStations()
    {

        $ret = new \stdClass();
        $ret->code = 200;
        $ret->body = '{"foo":"bar"}';

        $expected = new \stdClass();
        $expected->foo = 'bar';

        $this->restMock->expects($this->once())
            ->method('get')
            ->with('https://www.pegelonline.wsv.de/webservices/rest-api/v2/stations.json?includeTimeseries=false&includeCurrentMeasurement=false&includeCharacteristicValues=false')
            ->willReturn($ret);

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $stations = $pgOnline->getStations();
        $this->assertEquals($stations, $expected);
    }

    public function testGetStationByName()
    {
        $ret = new \stdClass();
        $ret->code = 200;
        $ret->body = '{
          "uuid": "593647aa-9fea-43ec-a7d6-6476a76ae868",
          "number": "2710080",
          "shortname": "BONN",
          "longname": "BONN",
        }';

        $expected = json_decode($ret->body);

        $this->restMock->expects($this->once())
            ->method('get')
            ->with('https://www.pegelonline.wsv.de/webservices/rest-api/v2/stations/BONN.json')
            ->willReturn($ret);

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $station = $pgOnline->getStationByName('bonn');
        $this->assertEquals($station, $expected);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Nothing found for given parameters.
     */
    public function test404Exception()
    {
        $ret = new \stdClass();
        $ret->code = 404;
        $ret->body = '{"message": "Nothing found for given parameters."}';

        $this->restMock->expects($this->once())
            ->method('get')
            ->willReturn($ret);

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $station = $pgOnline->getStationByName('xyz');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage foo
     */
    public function test0Exception()
    {
        $ret = new \stdClass();
        $ret->code = 0;
        $ret->error = new \stdClass();
        $ret->error->message = 'foo';

        $this->restMock->expects($this->once())
            ->method('get')
            ->willReturn($ret);

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $station = $pgOnline->getStationByName('xyz');
    }

    public function testGetMeasurement()
    {
        $this->restMock->expects($this->once())
            ->method('get')
            ->with('https://www.pegelonline.wsv.de/webservices/rest-api/v2/stations/BONN/W/measurements.json')
            ->willReturn($this->getReturn());

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $pgOnline->getMeasurementsForStation('bonn');
    }

    public function testGetMeasurementWithTimestamps()
    {
        $this->restMock->expects($this->once())
            ->method('get')
            ->with('https://www.pegelonline.wsv.de/webservices/rest-api/v2/stations/BONN/W/measurements.json?start=2016-10-20T10:00:00+02:00&end=2016-10-23T10:00:00+02:00')
            ->willReturn($this->getReturn());

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $pgOnline->getMeasurementsForStation('bonn', "2016-10-20T10:00:00+02:00", "2016-10-23T10:00:00+02:00");
    }

    public function testGetWaters()
    {
        $this->restMock->expects($this->once())
            ->method('get')
            ->with('https://www.pegelonline.wsv.de/webservices/rest-api/v2/waters.json?includeStations=false')
            ->willReturn($this->getReturn());

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $pgOnline->getWaters();
    }

    public function testGetWatersWithStations()
    {
        $this->restMock->expects($this->once())
            ->method('get')
            ->with('https://www.pegelonline.wsv.de/webservices/rest-api/v2/waters.json?includeStations=true')
            ->willReturn($this->getReturn());

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $pgOnline->getWaters(true);
    }

    public function testGetTimelineForStation()
    {
        $this->restMock->expects($this->once())
            ->method('get')
            ->with('https://www.pegelonline.wsv.de/webservices/rest-api/v2//stations/XYZ/W.json?includeCurrentMeasurement=true')
            ->willReturn($this->getReturn());

        $pgOnline = new Pegelonline\Pegelonline($this->restMock);
        $pgOnline->getTimelineForStation('xyz');
    }

    private function getReturn()
    {
        $ret = new \stdClass();
        $ret->code = 200;
        $ret->body = '{
            "timestamp": "2016-10-23T10:00:00+02:00",
            "value": 172.0
        }';

        return $ret;
    }
}
