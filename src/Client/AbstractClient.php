<?php

namespace Currobber\Client;

use Currobber\Result\PairRateData;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Abstract class for currency client
 * @author mkrasilnikov
 */
abstract class AbstractClient {
    /**
     * @var Client guzzle http client
     */
    private $HttpClient;

    /**
     * @param ResponseInterface $Response
     * @return PairRateData[]
     */
    abstract protected function parseResponse(ResponseInterface $Response);

    /**
     * Return guzzle http client
     * @return Client
     */
    protected function getHttpClient() {
        if (is_null($this->HttpClient)) {
            $this->HttpClient = new Client();
        }
        return $this->HttpClient;
    }

    /**
     * Return guzzle http request
     * @param string $uri
     * @return Request
     */
    protected function createGetRequest($uri) {
        return new Request('GET', $uri);
    }
}
