<?php

namespace Smartcat\ConnectorAPI\API\Model;

class UnitPriceViewModel
{
    /**
     * @var string
     */
    protected $unitType;
    /**
     * @var string
     */
    protected $currency;
    /**
     * @var float
     */
    protected $amount;
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
}