<?php

namespace ABBYY\CloudAPI\API\Model;

class ServicePriceViewModel
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $accountId;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $from;
    /**
     * @var string
     */
    protected $to;
    /**
     * @var UnitPriceViewModel[]
     */
    protected $unitPrices;
    /**
     * @var DiscountViewModel[]
     */
    protected $discounts;
    /**
     * @var \DateTime
     */
    protected $created;
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
    public function getAccountId()
    {
        return $this->accountId;
    }
    /**
     * @param string $accountId
     *
     * @return self
     */
    public function setAccountId($accountId = null)
    {
        $this->accountId = $accountId;
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
    public function getFrom()
    {
        return $this->from;
    }
    /**
     * @param string $from
     *
     * @return self
     */
    public function setFrom($from = null)
    {
        $this->from = $from;
        return $this;
    }
    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }
    /**
     * @param string $to
     *
     * @return self
     */
    public function setTo($to = null)
    {
        $this->to = $to;
        return $this;
    }
    /**
     * @return UnitPriceViewModel[]
     */
    public function getUnitPrices()
    {
        return $this->unitPrices;
    }
    /**
     * @param UnitPriceViewModel[] $unitPrices
     *
     * @return self
     */
    public function setUnitPrices(array $unitPrices = null)
    {
        $this->unitPrices = $unitPrices;
        return $this;
    }
    /**
     * @return DiscountViewModel[]
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }
    /**
     * @param DiscountViewModel[] $discounts
     *
     * @return self
     */
    public function setDiscounts(array $discounts = null)
    {
        $this->discounts = $discounts;
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
}