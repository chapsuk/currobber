<?php

namespace Currobber\Tests\Client\Yahoo;

use Currobber\Client\Yahoo\Client;
use Currobber\Result\PairRateData;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;

/**
 * Test Yahoo Client class
 * @author mkrasilnikov
 */
class ClientTest extends PHPUnit_Framework_TestCase {

    public function testCreateUri() {
        $Reflection = new ReflectionMethod(Client::class, 'createUri');
        $Reflection->setAccessible(true);
        $Instance = new Client();

        $correctUriForOne = 'http://query.yahooapis.com/v1/public/yql?q=select * from yahoo.finance.xchange where pair in (USDRUB)&env=http://datatables.org/alltables.env';
        $correctUriForMany = 'http://query.yahooapis.com/v1/public/yql?q=select * from yahoo.finance.xchange where pair in (USDRUB,EURRUB)&env=http://datatables.org/alltables.env';

        $this->assertEquals($correctUriForOne, $Reflection->invoke($Instance, ['USDRUB']));
        $this->assertEquals($correctUriForMany, $Reflection->invoke($Instance, ['USDRUB', 'EURRUB']));
    }

    public function testPreparePairName() {
        $Reflection = new ReflectionMethod(Client::class, 'preparePairName');
        $Reflection->setAccessible(true);
        $Instance = new Client();

        $correctRealPairName = '"USDRUB"';
        $this->assertEquals($correctRealPairName, $Reflection->invoke($Instance, 'USDRUB'));
    }

    public function testCreateRequest() {
        $Reflection = new ReflectionMethod(Client::class, 'createGetRequest');
        $Reflection->setAccessible(true);
        $Instance = new Client();

        $correctSheme = 'http';
        $correctHost  = 'query.yahooapis.com';
        $correctPath  = '/v1/public/yql';
        $correctQuery = 'q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22USDRUB%22)&env=http://datatables.org/alltables.env';

        /** @var Request $Request */
        $Request = $Reflection->invoke($Instance, $correctSheme. '://'.$correctHost.$correctPath.'?'.$correctQuery);

        $this->assertInstanceOf(Request::class, $Request);
        $this->assertEquals($correctSheme, $Request->getUri()->getScheme());
        $this->assertEquals($correctHost, $Request->getUri()->getHost());
        $this->assertEquals($correctPath, $Request->getUri()->getPath());
        $this->assertEquals($correctQuery, $Request->getUri()->getQuery());
        $this->assertEquals('GET', $Request->getMethod());
    }

    public function testParseResponse() {
        $realResult = '<?xml version="1.0" encoding="UTF-8"?>
<query xmlns:yahoo="http://www.yahooapis.com/v1/base.rng" yahoo:count="1" yahoo:created="2015-12-30T22:00:20Z" yahoo:lang="en-US"><results><rate id="USDRUB"><Name>USD/RUB</Name><Rate>73.5245</Rate><Date>12/30/2015</Date><Time>9:59pm</Time><Ask>73.7000</Ask><Bid>73.5245</Bid></rate></results></query><!-- total: 15 -->
<!-- main-e62d9638-ad59-11e5-ac7c-7446a0f459f0 -->
';

        $StreamMock = $this->getMockBuilder(Stream::class)
            ->disableOriginalConstructor()
            ->getMock();

        $StreamMock->method('getContents')
            ->willReturn($realResult);

        $ResponseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ResponseMock->method('getBody')
            ->will($this->returnValue($StreamMock));


        $Reflection = new ReflectionMethod(Client::class, 'parseResponse');
        $Reflection->setAccessible(true);
        $Instance = new Client();

        $Result = $Reflection->invoke($Instance, $ResponseMock);
        $this->assertCount(1, $Result);
        /** @var PairRateData $PairRate */
        $PairRate = current($Result);
        $this->assertInstanceOf(PairRateData::class, $PairRate);
        $this->assertEquals('USDRUB', $PairRate->getPairName());
        $this->assertEquals(73.5245, $PairRate->getRate());
        $this->assertEquals('2015-12-30', $PairRate->getDate());
    }

    public function testGet() {
        $Result = (new Client())->get('USDRUB');
        $this->assertInstanceOf(PairRateData::class, $Result);
        $this->assertEquals('USDRUB', $Result->getPairName());
        if ($Result->getRate() < 30) {
            $this->fail('ебать мы богаты');
        }
        $this->assertEquals(date('Y-m-d'), $Result->getDate());
    }

    public function testGetMulti() {
        $Result = (new Client())->getMulti(['USDRUB', 'EURRUB']);
        $this->assertCount(2, $Result);

        /** @var PairRateData $PairRate1 */
        $PairRate1 = current($Result);
        $this->assertInstanceOf(PairRateData::class, $PairRate1);
        $this->assertEquals('USDRUB', $PairRate1->getPairName());
        if ($PairRate1->getRate() < 30) {
            $this->fail('ебать мы богаты');
        }
        $this->assertEquals(date('Y-m-d'), $PairRate1->getDate());

        /** @var PairRateData $PairRate2 */
        $PairRate2 = end($Result);
        $this->assertInstanceOf(PairRateData::class, $PairRate2);
        $this->assertEquals('EURRUB', $PairRate2->getPairName());
        if ($PairRate2->getRate() < 40) {
            $this->fail('ебать мы богаты');
        }
        $this->assertEquals(date('Y-m-d'), $PairRate2->getDate());
    }
}
