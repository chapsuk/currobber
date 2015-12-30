<?php

namespace Currobber\Tests\Client\Currencylayer;

use Currobber\Client\Currencylayer\Client;
use Currobber\Result\PairQuoteData;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;

/**
 * Test currencylayer api client
 * @author mkrasilnikov
 */
class ClientTest extends PHPUnit_Framework_TestCase {

    public function testCreateInstance() {
        $accessKey = '63a1d124bcd8e88c3ad2cf7f2d0c3a8f';
        $Client = new Client($accessKey);
        $this->assertEquals($accessKey, $Client->getAccessKey());
        var_dump($Client->getMulti('USD', ['EUR', 'RUB'], '2015-10-10'));
    }

    public function testCreateUri() {
        $correctUriOne = 'http://apilayer.net/api/historical?source=USD&currencies=EUR&date=2015-10-10&access_key=testKey';
        $correctUriMany = 'http://apilayer.net/api/historical?source=USD&currencies=EUR,RUB&date=2015-10-10&access_key=testKey';

        $accessKey = 'testKey';
        $Reflection = new ReflectionMethod(Client::class, 'createUri');
        $Reflection->setAccessible(true);
        $Instance = new Client($accessKey);
        $this->assertEquals($correctUriOne, $Reflection->invoke($Instance, 'USD', ['EUR'], '2015-10-10'));
        $this->assertEquals($correctUriMany, $Reflection->invoke($Instance, 'USD', ['EUR', 'RUB'], '2015-10-10'));
    }

    public function testCreateRequest() {
        $correctUri = 'http://apilayer.net/api/historical?source=USD&currencies=EUR&date=2015-10-10&access_key=testKey';

        $accessKey = 'testKey';
        $Reflection = new ReflectionMethod(Client::class, 'createRequest');
        $Reflection->setAccessible(true);
        $Instance = new Client($accessKey);

        $correctSheme = 'http';
        $correctHost  = 'apilayer.net';
        $correctPath  = '/api/historical';
        $correctQuery = 'source=USD&currencies=EUR&date=2015-10-10&access_key=testKey';

        /** @var Request $Request */
        $Request = $Reflection->invoke($Instance, $correctUri);
        $this->assertInstanceOf(Request::class, $Request);
        $this->assertEquals($correctSheme, $Request->getUri()->getScheme());
        $this->assertEquals($correctHost, $Request->getUri()->getHost());
        $this->assertEquals($correctPath, $Request->getUri()->getPath());
        $this->assertEquals($correctQuery, $Request->getUri()->getQuery());
        $this->assertEquals('GET', $Request->getMethod());
    }

    public function testParseResponse() {
        $realResult = '{"success":true,"terms":"https://currencylayer.com/terms","privacy":"https://currencylayer.com/privacy","historical":true,"date":"2015-10-10","timestamp":1444521599,"source":"USD","quotes":{"USDEUR":0.880398}}';

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

        $accessKey = 'testKey';
        $Reflection = new ReflectionMethod(Client::class, 'parseResponse');
        $Reflection->setAccessible(true);
        $Instance = new Client($accessKey);

        $Result = $Reflection->invoke($Instance, $ResponseMock);
        $this->assertCount(1, $Result);
        /** @var PairQuoteData $PairRate */
        $PairRate = current($Result);
        $this->assertInstanceOf(PairQuoteData::class, $PairRate);
        $this->assertEquals('USDEUR', $PairRate->getPairName());
        $this->assertEquals(0.880398, $PairRate->getQuote());
        $this->assertEquals('2015-10-10', $PairRate->getDate());
    }
}
