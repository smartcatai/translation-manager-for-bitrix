<?php

namespace Smartcat\ConnectorAPI\API\Model;

class FileInfoViewModel
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $token;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $mime;
    /**
     * @var bool
     */
    protected $isRecognizable;
    /**
     * @var string[]
     */
    protected $expectedLanguages;
    /**
     * @var OcrSettingsViewModel
     */
    protected $ocrSettings;
    /**
     * @var TextStatisticsViewModel
     */
    protected $statistics;
    /**
     * @var OcrStatisticsViewModel
     */
    protected $ocrStatistics;
    /**
     * @var \DateTime
     */
    protected $created;
    /**
     * @var \DateTime
     */
    protected $processed;
    /**
     * @var \DateTime
     */
    protected $deleted;
    /**
     * @var int
     */
    protected $readingProgress;
    /**
     * @var string
     */
    protected $readingStatus;
    /**
     * @var OcrWarningViewModel[]
     */
    protected $ocrWarnings;
    /**
     * @var string
     */
    protected $error;
    /**
     * @var bool
     */
    protected $isDeleted;
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id = null)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * @param string $token
     *
     * @return self
     */
    public function setToken($token = null)
    {
        $this->token = $token;
        return $this;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name = null)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }
    /**
     * @param string $mime
     *
     * @return self
     */
    public function setMime($mime = null)
    {
        $this->mime = $mime;
        return $this;
    }
    /**
     * @return bool
     */
    public function getIsRecognizable()
    {
        return $this->isRecognizable;
    }
    /**
     * @param bool $isRecognizable
     *
     * @return self
     */
    public function setIsRecognizable($isRecognizable = null)
    {
        $this->isRecognizable = $isRecognizable;
        return $this;
    }
    /**
     * @return string[]
     */
    public function getExpectedLanguages()
    {
        return $this->expectedLanguages;
    }
    /**
     * @param string[] $expectedLanguages
     *
     * @return self
     */
    public function setExpectedLanguages(array $expectedLanguages = null)
    {
        $this->expectedLanguages = $expectedLanguages;
        return $this;
    }
    /**
     * @return OcrSettingsViewModel
     */
    public function getOcrSettings()
    {
        return $this->ocrSettings;
    }
    /**
     * @param OcrSettingsViewModel $ocrSettings
     *
     * @return self
     */
    public function setOcrSettings(OcrSettingsViewModel $ocrSettings = null)
    {
        $this->ocrSettings = $ocrSettings;
        return $this;
    }
    /**
     * @return TextStatisticsViewModel
     */
    public function getStatistics()
    {
        return $this->statistics;
    }
    /**
     * @param TextStatisticsViewModel $statistics
     *
     * @return self
     */
    public function setStatistics(TextStatisticsViewModel $statistics = null)
    {
        $this->statistics = $statistics;
        return $this;
    }
    /**
     * @return OcrStatisticsViewModel
     */
    public function getOcrStatistics()
    {
        return $this->ocrStatistics;
    }
    /**
     * @param OcrStatisticsViewModel $ocrStatistics
     *
     * @return self
     */
    public function setOcrStatistics(OcrStatisticsViewModel $ocrStatistics = null)
    {
        $this->ocrStatistics = $ocrStatistics;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * @param \DateTime $created
     *
     * @return self
     */
    public function setCreated(\DateTime $created = null)
    {
        $this->created = $created;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getProcessed()
    {
        return $this->processed;
    }
    /**
     * @param \DateTime $processed
     *
     * @return self
     */
    public function setProcessed(\DateTime $processed = null)
    {
        $this->processed = $processed;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
    /**
     * @param \DateTime $deleted
     *
     * @return self
     */
    public function setDeleted(\DateTime $deleted = null)
    {
        $this->deleted = $deleted;
        return $this;
    }
    /**
     * @return int
     */
    public function getReadingProgress()
    {
        return $this->readingProgress;
    }
    /**
     * @param int $readingProgress
     *
     * @return self
     */
    public function setReadingProgress($readingProgress = null)
    {
        $this->readingProgress = $readingProgress;
        return $this;
    }
    /**
     * @return string
     */
    public function getReadingStatus()
    {
        return $this->readingStatus;
    }
    /**
     * @param string $readingStatus
     *
     * @return self
     */
    public function setReadingStatus($readingStatus = null)
    {
        $this->readingStatus = $readingStatus;
        return $this;
    }
    /**
     * @return OcrWarningViewModel[]
     */
    public function getOcrWarnings()
    {
        return $this->ocrWarnings;
    }
    /**
     * @param OcrWarningViewModel[] $ocrWarnings
     *
     * @return self
     */
    public function setOcrWarnings(array $ocrWarnings = null)
    {
        $this->ocrWarnings = $ocrWarnings;
        return $this;
    }
    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * @param string $error
     *
     * @return self
     */
    public function setError($error = null)
    {
        $this->error = $error;
        return $this;
    }
    /**
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
    /**
     * @param bool $isDeleted
     *
     * @return self
     */
    public function setIsDeleted($isDeleted = null)
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }
}