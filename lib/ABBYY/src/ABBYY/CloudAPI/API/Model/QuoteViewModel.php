<?php

namespace ABBYY\CloudAPI\API\Model;

class QuoteViewModel
{
    /**
     * @var string
     */
    protected $fileId;
    /**
     * @var string
     */
    protected $from;
    /**
     * @var string
     */
    protected $to;
    /**
     * @var int
     */
    protected $unitCount;
    /**
     * @var float
     */
    protected $costPerUnit;
    /**
     * @var float
     */
    protected $amount;
    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }
    /**
     * @param string $fileId
     *
     * @return self
     */
    public function setFileId($fileId = null)
    {
        $this->fileId = $fileId;
        return $this;
    }
    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }
    /**
     * @param string $from
     *
     * @return self
     */
    public function setFrom($from = null)
    {
        $this->from = $from;
        return $this;
    }
    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }
    /**
     * @param string $to
     *
     * @return self
     */
    public function setTo($to = null)
    {
        $this->to = $to;
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
     * @return float
     */
    public function getCostPerUnit()
    {
        return $this->costPerUnit;
    }
    /**
     * @param float $costPerUnit
     *
     * @return self
     */
    public function setCostPerUnit($costPerUnit = null)
    {
        $this->costPerUnit = $costPerUnit;
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