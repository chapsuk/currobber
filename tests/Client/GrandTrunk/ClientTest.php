<?php

namespace Currobber\Tests\Client\GrandTrunk;

use Currobber\Client\GrandTrunk\Client;
use Currobber\Result\PairRateData;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;

/**
 * Test grund trunk api client class
 * @author mkrasilnikov
 */
class ClientTest extends PHPUnit_Framework_TestCase {

    public function testCreateUriForRange() {
        $correctUri = 'http://currencies.apps.grandtrunk.net/getrange/2015-10-10/2015-10-11/usd/eur';
        $Reflection = new ReflectionMethod(Client::class, 'createUriForRange');
        $Reflection->setAccessible(true);
        $Instance = new Client();
        $this->assertEquals($correctUri, $Reflection->invoke($Instance, 'USD', 'EUR', '2015-10-10', '2015-10-11'));
    }

    public function testCreateUriForOneRate() {
        $correctUri = 'http://currencies.apps.grandtrunk.net/getrate/2015-10-10/usd/eur';
        $Reflection = new ReflectionMethod(Client::class, 'createUriForOneRate');
        $Reflection->setAccessible(true);
        $Instance = new Client();
        $this->assertEquals($correctUri, $Reflection->invoke($Instance, 'USD', 'EUR', '2015-10-10'));
    }

    public function testGet() {
        $Client = new Client();
        $date = '2015-10-10';
        $Result = $Client->get('USD', 'EUR', $date);
        $this->assertInstanceOf(PairRateData::class, $Result);
        $this->assertEquals($date, $Result->getDate());
        // if has error, then source change pair rate
        $this->assertEquals(0.880365000003, $Result->getRate());
    }

    public function testGetForPeriod() {
        $Client = new Client();
        $begin = '2015-10-10';
        $end = '2015-10-15';
        $Result = $Client->getForPeriod('USD', 'EUR', $begin, $end);
        $this->assertCount(6, $Result);
        $beginTs = strtotime($begin);
        $endTs = strtotime($end);
        foreach ($Result as $Item) {
            $this->assertInstanceOf(PairRateData::class, $Item);
            $ts = strtotime($Item->getDate());
            if ($ts > $endTs || $ts < $beginTs) {
                $this->fail('date not in period');
            }
            $this->assertNotEmpty($Item->getRate());
        }
    }
}
