<?php

namespace Smartcat\ConnectorAPI\API\Model;

class OcrWarningViewModel
{
    /**
     * @var string
     */
    protected $warningType;
    /**
     * @var int
     */
    protected $pageNumber;
    /**
     * @var int
     */
    protected $dpi;
    /**
     * @var int
     */
    protected $languageCount;
    /**
     * @var string
     */
    protected $fullWarningMessage;
    /**
     * @return string
     */
    public function getWarningType()
    {
        return $this->warningType;
    }
    /**
     * @param string $warningType
     *
     * @return self
     */
    public function setWarningType($warningType = null)
    {
        $this->warningType = $warningType;
        return $this;
    }
    /**
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }
    /**
     * @param int $pageNumber
     *
     * @return self
     */
    public function setPageNumber($pageNumber = null)
    {
        $this->pageNumber = $pageNumber;
        return $this;
    }
    /**
     * @return int
     */
    public function getDpi()
    {
        return $this->dpi;
    }
    /**
     * @param int $dpi
     *
     * @return self
     */
    public function setDpi($dpi = null)
    {
        $this->dpi = $dpi;
        return $this;
    }
    /**
     * @return int
     */
    public function getLanguageCount()
    {
        return $this->languageCount;
    }
    /**
     * @param int $languageCount
     *
     * @return self
     */
    public function setLanguageCount($languageCount = null)
    {
        $this->languageCount = $languageCount;
        return $this;
    }
    /**
     * @return string
     */
    public function getFullWarningMessage()
    {
        return $this->fullWarningMessage;
    }
    /**
     * @param string $fullWarningMessage
     *
     * @return self
     */
    public function setFullWarningMessage($fullWarningMessage = null)
    {
        $this->fullWarningMessage = $fullWarningMessage;
        return $this;
    }
}