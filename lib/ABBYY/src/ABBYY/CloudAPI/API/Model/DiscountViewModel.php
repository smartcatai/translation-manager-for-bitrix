<?php

namespace ABBYY\CloudAPI\API\Model;

class DiscountViewModel
{
    /**
     * @var string
     */
    protected $discountType;
    /**
     * @var float
     */
    protected $discount;
    /**
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }
    /**
     * @param string $discountType
     *
     * @return self
     */
    public function setDiscountType($discountType = null)
    {
        $this->discountType = $discountType;
        return $this;
    }
    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }
    /**
     * @param float $discount
     *
     * @return self
     */
    public function setDiscount($discount = null)
    {
        $this->discount = $discount;
        return $this;
    }
}