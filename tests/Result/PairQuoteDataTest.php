<?php

namespace Currobber\Tests\Result;

use Currobber\Result\PairQuoteData;
use LogicException;
use PHPUnit_Framework_TestCase;

/**
 * Test PairRateData class
 * @author mkrasilnikov
 */
class PairQuoteDataTest extends PHPUnit_Framework_TestCase {

    public function testCreateData() {
        $pairName = 'USDRUB';
        $quote = 0.15;
        $date = '2015-11-07';

        $Data = new PairQuoteData($pairName, $quote, $date);
        $this->assertEquals($pairName, $Data->getPairName());
        $this->assertEquals($quote, $Data->getQuote());
        $this->assertEquals($date, $Data->getDate());
    }

    public function testWrongData() {
        $this->setExpectedException(LogicException::class);

        new PairQuoteData('USDEUR', 1.02, '2015-13-13');
    }
}
