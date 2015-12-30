<?php

namespace Currobber\Client\Currencylayer;

use Currobber\Client\AbstractClient;
use Currobber\Result\PairQuoteData;
use GuzzleHttp\Psr7\Request;
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
     * Return guzzle http request
     * @param string $uri
     * @return Request
     */
    private function createRequest($uri) {
        return new Request('GET', $uri);
    }

    /**
     * Return array of pair quotes
     * @param ResponseInterface $Response
     * @return PairQuoteData[]
     */
    private function parseResponse(ResponseInterface $Response) {
        $response = json_decode($Response->getBody()->getContents(), true);
        if (!$response['success']) {
            throw new LogicException('error response: ' . json_encode($response));
        }
        $result = [];
        foreach ($response['quotes'] as $pairName => $quote) {
            $result[] = new PairQuoteData(
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
     * @return PairQuoteData
     */
    public function get($sourceCurrency, $toCurrency, $date) {
        $uri = $this->createUri($sourceCurrency, [$toCurrency], $date);
        $Request = $this->createRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $Result = $this->parseResponse($Response);
        return current($Result);
    }

    /**
     * Return array of pair quotes data
     * @param string $sourceCurrency (free subscription support USD source only)
     * @param array $toCurrencies
     * @param string $date
     * @return PairQuoteData[]
     */
    public function getMulti($sourceCurrency, array $toCurrencies, $date) {
        $uri = $this->createUri($sourceCurrency, $toCurrencies, $date);
        $Request = $this->createRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $Result = $this->parseResponse($Response);
        return $Result;
    }
}
