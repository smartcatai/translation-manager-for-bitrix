<?php

namespace Smartcat\ConnectorAPI\API\Model;

class OrderStatisticsViewModel
{
    /**
     * @var int
     */
    protected $documentsCount;
    /**
     * @var int
     */
    protected $pagesCount;
    /**
     * @var int
     */
    protected $wordsCount;
    /**
     * @var int
     */
    protected $charsCount;
    /**
     * @return int
     */
    public function getDocumentsCount()
    {
        return $this->documentsCount;
    }
    /**
     * @param int $documentsCount
     *
     * @return self
     */
    public function setDocumentsCount($documentsCount = null)
    {
        $this->documentsCount = $documentsCount;
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
}