<?php

namespace Currobber\Client\OpenExchangeRates;

use PHPUnit_Framework_TestCase;

/**
 * Test open exchange rates api client class
 * @author mkrasilnikov
 */
class ClientTest extends PHPUnit_Framework_TestCase {

    public function testCreate() {
        $this->markTestSkipped('open exchange rates support only current rate with free subscription');
        $appId = '<APP_ID>';
        $Client = new Client($appId);
        $Result = $Client->get('USD', 'RUB', '2011-11-11');
        //  oh, open exchange rates api with free subscription support only current date rates :(
    }
}
