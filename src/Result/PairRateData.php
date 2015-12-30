<?php

namespace Currobber\Result;

use LogicException;

/**
 * Class of result pair data
 * @author mkrasilnikov
 */
class PairRateData {
    /**
     * @var string name of currency pair, e.g. USD/RUB
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
        $this->pairName = $pairName;
        $this->rate = $rate;

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
     * Return pair rate
     * @return int
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
