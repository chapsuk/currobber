<?php

namespace Currobber\Client\OpenExchangeRates;

use PHPUnit_Framework_TestCase;

/**
 * Test open exchange rates api client class
 * @author mkrasilnikov
 */
class ClientTest extends PHPUnit_Framework_TestCase {

    public function testCreate() {
        $appId = '<APP_ID>';
        $Client = new Client($appId);
        var_dump($Client->get('USD', 'RUB', '2015-10-10'));
        // oh, open exchange rates api with free subscription support only current date quotes :(
    }
}
