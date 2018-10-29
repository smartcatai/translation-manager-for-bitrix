<?php

namespace Smartcat\ConnectorAPI\API\Model;

class OrdersPageViewModel
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
     * @var int
     */
    protected $count;
    /**
     * @var string
     */
    protected $status;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var OrderViewModel[]
     */
    protected $orders;
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
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
    /**
     * @param int $count
     *
     * @return self
     */
    public function setCount($count = null)
    {
        $this->count = $count;
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
    /**
     * @return OrderViewModel[]
     */
    public function getOrders()
    {
        return $this->orders;
    }
    /**
     * @param OrderViewModel[] $orders
     *
     * @return self
     */
    public function setOrders(array $orders = null)
    {
        $this->orders = $orders;
        return $this;
    }
}