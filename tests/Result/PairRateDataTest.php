<?php

namespace Currobber\Tests\Result;

use Currobber\Result\PairRateData;
use LogicException;
use PHPUnit_Framework_TestCase;

/**
 * Test PairRateData class
 * @author mkrasilnikov
 */
class PairRateDataTest extends PHPUnit_Framework_TestCase {

    public function testCreateData() {
        $pairName = 'USDRUB';
        $rate = 0.15;
        $date = '2015-11-07';

        $Data = new PairRateData($pairName, $rate, $date);
        $this->assertEquals($pairName, $Data->getPairName());
        $this->assertEquals($rate, $Data->getRate());
        $this->assertEquals($date, $Data->getDate());
    }

    public function testWrongData() {
        $this->setExpectedException(LogicException::class);

        new PairRateData('USDEUR', 1.02, '2015-13-13');
    }
}
