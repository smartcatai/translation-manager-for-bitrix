<?php

namespace Smartcat\ConnectorAPI\API\Model;

class GetProposalModel
{
    /**
     * @var string
     */
    protected $costType;
    /**
     * @var string
     */
    protected $unitType;
    /**
     * @var string
     */
    protected $currency;
    /**
     * @var string
     */
    protected $from;
    /**
     * @var string[]
     */
    protected $to;
    /**
     * @var GetFileModel[]
     */
    protected $files;
    /**
     * @return string
     */
    public function getCostType()
    {
        return $this->costType;
    }
    /**
     * @param string $costType
     *
     * @return self
     */
    public function setCostType($costType = null)
    {
        $this->costType = $costType;
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
     * @return string[]
     */
    public function getTo()
    {
        return $this->to;
    }
    /**
     * @param string[] $to
     *
     * @return self
     */
    public function setTo(array $to = null)
    {
        $this->to = $to;
        return $this;
    }
    /**
     * @return GetFileModel[]
     */
    public function getFiles()
    {
        return $this->files;
    }
    /**
     * @param GetFileModel[] $files
     *
     * @return self
     */
    public function setFiles(array $files = null)
    {
        $this->files = $files;
        return $this;
    }
}