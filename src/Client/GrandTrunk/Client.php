<?php

namespace Currobber\Client\GrandTrunk;

use Currobber\Client\AbstractClient;
use Currobber\Result\PairRateData;
use Psr\Http\Message\ResponseInterface;

/**
 * http://currencies.apps.grandtrunk.net/
 * @author mkrasilnikov
 */
class Client extends AbstractClient {

    /**
     * @param ResponseInterface $Response
     * @return PairRateData[]
     */
    protected function parseResponse(ResponseInterface $Response) {
        // TODO: Implement parseResponse() method.
    }
}
