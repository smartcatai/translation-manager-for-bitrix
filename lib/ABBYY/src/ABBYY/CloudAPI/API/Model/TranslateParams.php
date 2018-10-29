<?php

namespace Smartcat\ConnectorAPI\API\Model;

class TranslateParams
{
    /**
     * @var string
     */
    protected $sourceLanguage;
    /**
     * @var string
     */
    protected $targetLanguage;
    /**
     * @var string
     */
    protected $sourceText;
    /**
     * @return string
     */
    public function getSourceLanguage()
    {
        return $this->sourceLanguage;
    }
    /**
     * @param string $sourceLanguage
     *
     * @return self
     */
    public function setSourceLanguage($sourceLanguage = null)
    {
        $this->sourceLanguage = $sourceLanguage;
        return $this;
    }
    /**
     * @return string
     */
    public function getTargetLanguage()
    {
        return $this->targetLanguage;
    }
    /**
     * @param string $targetLanguage
     *
     * @return self
     */
    public function setTargetLanguage($targetLanguage = null)
    {
        $this->targetLanguage = $targetLanguage;
        return $this;
    }
    /**
     * @return string
     */
    public function getSourceText()
    {
        return $this->sourceText;
    }
    /**
     * @param string $sourceText
     *
     * @return self
     */
    public function setSourceText($sourceText = null)
    {
        $this->sourceText = $sourceText;
        return $this;
    }
}