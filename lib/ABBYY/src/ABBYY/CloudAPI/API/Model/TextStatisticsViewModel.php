<?php

namespace ABBYY\CloudAPI\API\Model;

class TextStatisticsViewModel
{
    /**
     * @var string
     */
    protected $language;
    /**
     * @var int
     */
    protected $charsCount;
    /**
     * @var int
     */
    protected $wordsCount;
    /**
     * @var int
     */
    protected $pagesCount;
    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
    /**
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language = null)
    {
        $this->language = $language;
        return $this;
    }
    /**
     * @return int
     */
    public function getCharsCount()
    {
        return $this->charsCount;
    }
    /**
     * @param int $charsCount
     *
     * @return self
     */
    public function setCharsCount($charsCount = null)
    {
        $this->charsCount = $charsCount;
        return $this;
    }
    /**
     * @return int
     */
    public function getWordsCount()
    {
        return $this->wordsCount;
    }
    /**
     * @param int $wordsCount
     *
     * @return self
     */
    public function setWordsCount($wordsCount = null)
    {
        $this->wordsCount = $wordsCount;
        return $this;
    }
    /**
     * @return int
     */
    public function getPagesCount()
    {
        return $this->pagesCount;
    }
    /**
     * @param int $pagesCount
     *
     * @return self
     */
    public function setPagesCount($pagesCount = null)
    {
        $this->pagesCount = $pagesCount;
        return $this;
    }
}