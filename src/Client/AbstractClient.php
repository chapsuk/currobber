<?php
/*
 * Copyright 2015 Maksim Krasilnikov <jo1nk1k@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Currobber\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

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

    /**
     * Return guzzle http request
     * @param string $uri
     * @return Request
     */
    protected function createGetRequest($uri) {
        return new Request('GET', $uri);
    }
}
