<?php

namespace Currobber\Client\Yahoo;

use Currobber\Client\AbstractClient;
use Currobber\Result\PairQuoteData;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

/**
 * Yahoo currency client, can get quotes only current date
 * @author mkrasilnikov
 */
class Client extends AbstractClient {
    /**
     * Endpoint for query
     */
    const ENDPOINT = 'http://query.yahooapis.com/v1/public/yql';

    /**
     * Template for query, set to get params
     */
    const QUERY_TPL = 'select * from yahoo.finance.xchange where pair in (%s)';

    /**
     * Return uri for query
     * @param array $pairs
     * @return string
     */
    private function createUri(array $pairs) {
        return sprintf(
            "%s?q=%s&env=store://datatables.org/alltableswithkeys",
            self::ENDPOINT,
            sprintf(self::QUERY_TPL, implode(',', $pairs))
        );
    }

    /**
     * Return pair name for query
     * @return string
     */
    private function preparePairName($pairName) {
        return '"' . $pairName . '"';
    }

    /**
     * Return guzzle request object
     * @param string $uri
     * @return Request
     */
    private function createRequest($uri) {
        return new Request('GET', $uri);
    }

    /**
     * Convert guzzle response to our result
     * @param ResponseInterface $Response
     * @return PairQuoteData[]
     */
    private function parseResponse(ResponseInterface $Response) {
        $Chart = new SimpleXMLElement($Response->getBody()->getContents());
        $result = [];
        if (isset($Chart->results->rate)) {
            foreach ($Chart->results->rate as $Pair) {
                $result[] = new PairQuoteData(
                    (string) $Pair->Name,
                    (float) $Pair->Rate,
                    date('Y-m-d', strtotime($Pair->Date))
                );
            }
        }
        return $result;
    }

    /**
     * Return pair quote
     * @param string $pair pair name, e.g. "EURRUB"
     * @return PairQuoteData
     */
    public function get($pair) {
        $realPairName = $this->preparePairName($pair);
        $uri = $this->createUri([$realPairName]);
        $Request = $this->createRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $Result = $this->parseResponse($Response);
        return current($Result);
    }

    /**
     * Return array of pair quotes data objects
     * @param array $pairs pairs name, e.g. ["EURRUB", "USDRUB"]
     * @return PairQuoteData[]
     */
    public function getMulti(array $pairs) {
        $realPairNames = array_map(function ($pair) {
            return $this->preparePairName($pair);
        }, $pairs);
        $uri = $this->createUri($realPairNames);
        $Request = $this->createRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $Result = $this->parseResponse($Response);
        return $Result;
    }
}
