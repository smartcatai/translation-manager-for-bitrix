<?php

namespace ABBYY\CloudAPI\API\Model;

class TranslateResponse
{
    /**
     * @var string
     */
    protected $translation;
    /**
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }
    /**
     * @param string $translation
     *
     * @return self
     */
    public function setTranslation($translation = null)
    {
        $this->translation = $translation;
        return $this;
    }
}