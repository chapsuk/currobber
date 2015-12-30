<?php

namespace Currobber\Client;

use GuzzleHttp\Client;

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
     * Return guzzle http client
     * @return Client
     */
    protected function getHttpClient() {
        if (is_null($this->HttpClient)) {
            $this->HttpClient = new Client();
        }
        return $this->HttpClient;
    }
}
