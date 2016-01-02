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
namespace Currobber\Client\Currencylayer;

use Currobber\Client\AbstractClient;
use Currobber\Result\PairRateData;
use LogicException;
use Psr\Http\Message\ResponseInterface;

/**
 * Client for currencylayer api, free subscription support USD source only
 * @author mkrasilnikov
 */
class Client extends AbstractClient {
    /**
     * Currencylayer api endpoint
     */
    const ENDPOINT = 'http://apilayer.net/api/historical';

    /**
     * @var string access key
     */
    private $accessKey = '';

    /**
     * Client constructor.
     * @param string $accessKey
     */
    public function __construct($accessKey) {
        $this->accessKey = (string) $accessKey;
    }

    /**
     * Return api access key
     * @return string
     */
    public function getAccessKey() {
        return $this->accessKey;
    }

    /**
     * Return uri string
     * @param string $sourceCurrency
     * @param string[] $toCurrencies
     * @param string $date
     * @return string
     */
    private function createUri($sourceCurrency, array $toCurrencies, $date) {
        return sprintf(
            "%s?source=%s&currencies=%s&date=%s&access_key=%s",
            self::ENDPOINT,
            $sourceCurrency,
            implode(',', $toCurrencies),
            $date,
            $this->getAccessKey()
        );
    }

    /**
     * Return array of pair quotes
     * @param ResponseInterface $Response
     * @return PairRateData[]
     */
    protected function parseResponse(ResponseInterface $Response) {
        $response = json_decode($Response->getBody()->getContents(), true);
        if (!$response['success']) {
            throw new LogicException('error response: ' . json_encode($response));
        }
        $result = [];
        foreach ($response['quotes'] as $pairName => $quote) {
            $result[] = new PairRateData(
                $pairName,
                $quote,
                $response['date']
            );
        }
        return $result;
    }

    /**
     * Return pair quote data
     * @param string $sourceCurrency (free subscription support USD source only)
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
    public function getMulti($sourceCurrency, array $toCurrencies, $date) {
        $uri = $this->createUri($sourceCurrency, $toCurrencies, $date);
        $Request = $this->createGetRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $Result = $this->parseResponse($Response);
        return $Result;
    }
}
