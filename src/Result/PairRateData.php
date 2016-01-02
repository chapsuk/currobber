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
namespace Currobber\Result;

use LogicException;

/**
 * Class of result pair rate data
 * @author mkrasilnikov
 */
class PairRateData {
    /**
     * @var string name of currency pair, e.g. USDRUB
     */
    private $pairName = '';

    /**
     * @var int currency pair rate
     */
    private $rate = 0;

    /**
     * @var string date of currency rate, Y-m-d
     */
    private $date = '';

    public function __construct($pairName, $rate, $date) {
        $this->pairName = str_replace('/', '', $pairName);
        $this->rate     = (float) $rate;

        if (date('Y-m-d', strtotime($date)) != $date) {
            throw new LogicException('invalid date format, expected Y-m-d, gotten: ' . $date);
        }
        $this->date = $date;
    }

    /**
     * Return pair name
     * @return string
     */
    public function getPairName() {
        return $this->pairName;
    }

    /**
     * Return pair quote
     * @return float
     */
    public function getRate() {
        return $this->rate;
    }

    /**
     * Return date of pair rate
     * @return string
     */
    public function getDate() {
        return $this->date;
    }
}
