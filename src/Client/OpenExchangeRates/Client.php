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
namespace Currobber\Client\OpenExchangeRates;

use Currobber\Client\AbstractClient;
use Currobber\Result\PairRateData;
use Psr\Http\Message\ResponseInterface;

/**
 * Open Exchange Rates api client
 * Free subscription support only latest rates, we have yahoo client, that api bad for free
 * @author mkrasilnikov
 */
class Client extends AbstractClient {
    /**
     * API endpoint
     */
    const ENDPOINT = 'https://openexchangerates.org/api/historical/';

    /**
     * @var string app id
     */
    private $appId = '';

    /**
     * Client constructor.
     * @param string $appId
     */
    public function __construct($appId) {
        $this->appId = $appId;
    }

    /**
     * Return app id
     * @return string
     */
    public function getAppId() {
        return $this->appId;
    }

    /**
     * Return uri
     * @param string $base
     * @param array $toCurrencies
     * @param string $date
     * @return string
     */
    private function createUri($base, array $toCurrencies, $date) {
        return sprintf(
            "%s%s.json?app_id=%s&base=%s&symbols=%s",
            self::ENDPOINT,
            $date,
            $this->getAppId(),
            $base,
            implode(',', $toCurrencies)
        );
    }

    /**
     * Return array of pair quotes
     * @param ResponseInterface $Response
     * @return PairRateData[]
     */
    protected function parseResponse(ResponseInterface $Response) {
        $response = json_decode($Response->getBody()->getContents(), true);
        $result = [];
        $date = date('Y-m-d', $response['timestamp']);
        foreach ($response['rates'] as $toCurrency => $quote) {
            $result[] = new PairRateData(
                $response['base'].$toCurrency,
                $quote,
                $date
            );
        }
        return $result;
    }

    /**
     * Return pair quote data
     * @param string $sourceCurrency
     * @param string $toCurrency
     * @param string $date
     * @return PairRateData
     */
    public function get($sourceCurrency, $toCurrency, $date) {
        $uri = $this->createUri($sourceCurrency, [$toCurrency], $date);
        $Request = $this->createGetRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $Result = $this->parseResponse($Response);
        return current($Result);
    }

    /**
     * Return array of pair quotes data
     * @param string $sourceCurrency (free subscription support USD source only)
     * @param array $toCurrencies
     * @param string $date
     * @return PairRateData[]
     */
    public function getMulti($sourceCurrency, $toCurrencies, $date) {
        $uri = $this->createUri($sourceCurrency, $toCurrencies, $date);
        $Request = $this->createGetRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $Result = $this->parseResponse($Response);
        return $Result;
    }
}
