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
namespace Currobber\Client\GrandTrunk;

use Currobber\Client\AbstractClient;
use Currobber\Result\PairRateData;
use LogicException;

/**
 * Client for grand trunk api
 * http://currencies.apps.grandtrunk.net/
 * @author mkrasilnikov
 */
class Client extends AbstractClient {
    /**
     * Grund trunk api endpoint for range query
     */
    const RANGE_ENDPOINT_TPL = 'http://currencies.apps.grandtrunk.net/getrange/%s/%s/%s/%s';

    /**
     * Grund trunk api endpoint for one rate query
     */
    const SINGLE_DATE_ENDPOINT_TPL = 'http://currencies.apps.grandtrunk.net/getrate/%s/%s/%s';

    /**
     * Return uri string
     * @param string $fromDate
     * @param string $toDate
     * @param string $fromCode
     * @param string $toCode
     * @return string
     */
    private function createUriForRange($fromCode, $toCode, $fromDate, $toDate) {
        if (strtotime($toDate) <= strtotime($fromDate)) {
            throw new LogicException('fromdate needs to be before todate');
        }
        $fromCode = strtolower($fromCode);
        $toCode = strtolower($toCode);
        return sprintf(
            self::RANGE_ENDPOINT_TPL,
            $fromDate,
            $toDate,
            $fromCode,
            $toCode
        );
    }

    /**
     * Return uri string
     * @param string $fromCode
     * @param string $toCode
     * @param string $date
     * @return string
     */
    private function createUriForOneRate($fromCode, $toCode, $date) {
        $fromCode = strtolower($fromCode);
        $toCode = strtolower($toCode);
        return sprintf(
            self::SINGLE_DATE_ENDPOINT_TPL,
            $date,
            $fromCode,
            $toCode
        );
    }

    /**
     * Return pair quote data
     * @param string $fromCode
     * @param string $toCode
     * @param string $date
     * @return PairRateData
     */
    public function get($fromCode, $toCode, $date) {
        $uri = $this->createUriForOneRate($fromCode, $toCode, $date);
        $Request = $this->createGetRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $value = (float) $Response->getBody()->getContents();
        if ($value <= 0) {
            return null;
        }
        return new PairRateData(
            strtoupper($fromCode) . strtoupper($toCode),
            $value,
            $date
        );
    }

    /**
     * Return array of pair quotes data
     * @param string $fromCode
     * @param string $toCode
     * @param string $fromDate
     * @param string $toDate
     * @return PairRateData[]
     */
    public function getForPeriod($fromCode, $toCode, $fromDate, $toDate) {
        $uri = $this->createUriForRange($fromCode, $toCode, $fromDate, $toDate);
        $Request = $this->createGetRequest($uri);
        $Response = $this->getHttpClient()->send($Request);
        $response = $Response->getBody()->getContents();
        if (!$response) {
            return [];
        }
        $items = explode("\n", $response);
        $result = [];
        foreach ($items as $item) {
            if (!$item) {
                continue;
            }
            $rate = explode(" ", $item);
            $result[] = new PairRateData(
                strtoupper($fromCode).strtoupper($toCode),
                $rate[1],
                $rate[0]
            );
        }
        return $result;
    }
}
