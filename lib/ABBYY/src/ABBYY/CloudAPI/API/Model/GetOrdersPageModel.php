<?php

namespace ABBYY\CloudAPI\API\Model;

class GetOrdersPageModel
{
    /**
     * @var int
     */
    protected $skip;
    /**
     * @var int
     */
    protected $take;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $status;
    /**
     * @var string[]
     */
    protected $orderIds;
    /**
     * @var bool
     */
    protected $isDeleted;
    /**
     * @var string
     */
    protected $email;
    /**
     * @return int
     */
    public function getSkip()
    {
        return $this->skip;
    }
    /**
     * @param int $skip
     *
     * @return self
     */
    public function setSkip($skip = null)
    {
        $this->skip = $skip;
        return $this;
    }
    /**
     * @return int
     */
    public function getTake()
    {
        return $this->take;
    }
    /**
     * @param int $take
     *
     * @return self
     */
    public function setTake($take = null)
    {
        $this->take = $take;
        return $this;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type = null)
    {
        $this->type = $type;
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
    /**
     * @return string[]
     */
    public function getOrderIds()
    {
        return $this->orderIds;
    }
    /**
     * @param string[] $orderIds
     *
     * @return self
     */
    public function setOrderIds(array $orderIds = null)
    {
        $this->orderIds = $orderIds;
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
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email = null)
    {
        $this->email = $email;
        return $this;
    }
}