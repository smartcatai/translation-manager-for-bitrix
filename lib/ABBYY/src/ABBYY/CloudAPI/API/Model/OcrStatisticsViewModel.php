<?php

namespace Smartcat\ConnectorAPI\API\Model;

class OcrStatisticsViewModel
{
    /**
     * @var int
     */
    protected $exportedPages;
    /**
     * @var int
     */
    protected $totalCharacters;
    /**
     * @var int
     */
    protected $uncertainCharacters;
    /**
     * @var float
     */
    protected $successPart;
    /**
     * @return int
     */
    public function getExportedPages()
    {
        return $this->exportedPages;
    }
    /**
     * @param int $exportedPages
     *
     * @return self
     */
    public function setExportedPages($exportedPages = null)
    {
        $this->exportedPages = $exportedPages;
        return $this;
    }
    /**
     * @return int
     */
    public function getTotalCharacters()
    {
        return $this->totalCharacters;
    }
    /**
     * @param int $totalCharacters
     *
     * @return self
     */
    public function setTotalCharacters($totalCharacters = null)
    {
        $this->totalCharacters = $totalCharacters;
        return $this;
    }
    /**
     * @return int
     */
    public function getUncertainCharacters()
    {
        return $this->uncertainCharacters;
    }
    /**
     * @param int $uncertainCharacters
     *
     * @return self
     */
    public function setUncertainCharacters($uncertainCharacters = null)
    {
        $this->uncertainCharacters = $uncertainCharacters;
        return $this;
    }
    /**
     * @return float
     */
    public function getSuccessPart()
    {
        return $this->successPart;
    }
    /**
     * @param float $successPart
     *
     * @return self
     */
    public function setSuccessPart($successPart = null)
    {
        $this->successPart = $successPart;
        return $this;
    }
}