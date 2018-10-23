<?php

namespace ABBYY\CloudAPI\API\Model;

class OcrSettingsViewModel
{
    /**
     * @var string
     */
    protected $format;
    /**
     * @var string
     */
    protected $quality;
    /**
     * @var string
     */
    protected $mode;
    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
    /**
     * @param string $format
     *
     * @return self
     */
    public function setFormat($format = null)
    {
        $this->format = $format;
        return $this;
    }
    /**
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }
    /**
     * @param string $quality
     *
     * @return self
     */
    public function setQuality($quality = null)
    {
        $this->quality = $quality;
        return $this;
    }
    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
    /**
     * @param string $mode
     *
     * @return self
     */
    public function setMode($mode = null)
    {
        $this->mode = $mode;
        return $this;
    }
}