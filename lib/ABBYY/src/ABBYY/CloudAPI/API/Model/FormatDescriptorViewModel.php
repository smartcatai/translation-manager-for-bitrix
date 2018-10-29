<?php

namespace Smartcat\ConnectorAPI\API\Model;

class FormatDescriptorViewModel
{
    /**
     * @var string
     */
    protected $extension;
    /**
     * @var string
     */
    protected $mimeType;
    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
    /**
     * @param string $extension
     *
     * @return self
     */
    public function setExtension($extension = null)
    {
        $this->extension = $extension;
        return $this;
    }
    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
    /**
     * @param string $mimeType
     *
     * @return self
     */
    public function setMimeType($mimeType = null)
    {
        $this->mimeType = $mimeType;
        return $this;
    }
}