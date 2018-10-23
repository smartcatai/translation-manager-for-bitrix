<?php

namespace ABBYY\CloudAPI\API\Model;

class FileLinkViewModel
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