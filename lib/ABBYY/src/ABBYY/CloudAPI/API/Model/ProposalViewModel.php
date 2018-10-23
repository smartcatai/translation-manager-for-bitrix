<?php

namespace ABBYY\CloudAPI\API\Model;

class ProposalViewModel
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $leadTime;
    /**
     * @var string
     */
    protected $unitType;
    /**
     * @var int
     */
    protected $unitCount;
    /**
     * @var string
     */
    protected $currency;
    /**
     * @var float
     */
    protected $amount;
    /**
     * @var QuoteViewModel[]
     */
    protected $quotes;
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type = null)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * @return string
     */
    public function getLeadTime()
    {
        return $this->leadTime;
    }
    /**
     * @param string $leadTime
     *
     * @return self
     */
    public function setLeadTime($leadTime = null)
    {
        $this->leadTime = $leadTime;
        return $this;
    }
    /**
     * @return string
     */
    public function getUnitType()
    {
        return $this->unitType;
    }
    /**
     * @param string $unitType
     *
     * @return self
     */
    public function setUnitType($unitType = null)
    {
        $this->unitType = $unitType;
        return $this;
    }
    /**
     * @return int
     */
    public function getUnitCount()
    {
        return $this->unitCount;
    }
    /**
     * @param int $unitCount
     *
     * @return self
     */
    public function setUnitCount($unitCount = null)
    {
        $this->unitCount = $unitCount;
        return $this;
    }
    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
    /**
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency($currency = null)
    {
        $this->currency = $currency;
        return $this;
    }
    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }
    /**
     * @param float $amount
     *
     * @return self
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;
        return $this;
    }
    /**
     * @return QuoteViewModel[]
     */
    public function getQuotes()
    {
        return $this->quotes;
    }
    /**
     * @param QuoteViewModel[] $quotes
     *
     * @return self
     */
    public function setQuotes(array $quotes = null)
    {
        $this->quotes = $quotes;
        return $this;
    }
}