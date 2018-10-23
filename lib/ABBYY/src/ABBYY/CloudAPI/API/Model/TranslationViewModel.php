<?php

namespace ABBYY\CloudAPI\API\Model;

class TranslationViewModel
{
    /**
     * @var FileLinkViewModel
     */
    protected $sourceFile;
    /**
     * @var FileLinkViewModel
     */
    protected $targetFile;
    /**
     * @var \DateTime
     */
    protected $started;
    /**
     * @var \DateTime
     */
    protected $delivered;
    /**
     * @var int[]
     */
    protected $progress;
    /**
     * @var string
     */
    protected $status;
    /**
     * @return FileLinkViewModel
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }
    /**
     * @param FileLinkViewModel $sourceFile
     *
     * @return self
     */
    public function setSourceFile(FileLinkViewModel $sourceFile = null)
    {
        $this->sourceFile = $sourceFile;
        return $this;
    }
    /**
     * @return FileLinkViewModel
     */
    public function getTargetFile()
    {
        return $this->targetFile;
    }
    /**
     * @param FileLinkViewModel $targetFile
     *
     * @return self
     */
    public function setTargetFile(FileLinkViewModel $targetFile = null)
    {
        $this->targetFile = $targetFile;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getStarted()
    {
        return $this->started;
    }
    /**
     * @param \DateTime $started
     *
     * @return self
     */
    public function setStarted(\DateTime $started = null)
    {
        $this->started = $started;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getDelivered()
    {
        return $this->delivered;
    }
    /**
     * @param \DateTime $delivered
     *
     * @return self
     */
    public function setDelivered(\DateTime $delivered = null)
    {
        $this->delivered = $delivered;
        return $this;
    }
    /**
     * @return int[]
     */
    public function getProgress()
    {
        return $this->progress;
    }
    /**
     * @param int[] $progress
     *
     * @return self
     */
    public function setProgress(\ArrayObject $progress = null)
    {
        $this->progress = $progress;
        return $this;
    }
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status = null)
    {
        $this->status = $status;
        return $this;
    }
}