<?php

namespace Currobber\Result;

use LogicException;

/**
 * Class of result pair quote data
 * @author mkrasilnikov
 */
class PairQuoteData {
    /**
     * @var string name of currency pair, e.g. USD/RUB
     */
    private $pairName = '';

    /**
     * @var int currency pair quote
     */
    private $quote = 0;

    /**
     * @var string date of currency rate, Y-m-d
     */
    private $date = '';

    public function __construct($pairName, $quote, $date) {
        $this->pairName = (string) $pairName;
        $this->quote    = (float) $quote;

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
    public function getQuote() {
        return $this->quote;
    }

    /**
     * Return date of pair rate
     * @return string
     */
    public function getDate() {
        return $this->date;
    }
}
