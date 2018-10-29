<?php

namespace Smartcat\ConnectorAPI\API\Model;

class FullOrderViewModel
{
    /**
     * @var TranslationViewModel[]
     */
    protected $translations;
    /**
     * @var string
     */
    protected $id;
    /**
     * @var int
     */
    protected $number;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $from;
    /**
     * @var string[]
     */
    protected $to;
    /**
     * @var bool
     */
    protected $isLayoutRequired;
    /**
     * @var string
     */
    protected $label;
    /**
     * @var string
     */
    protected $paymentType;
    /**
     * @var string
     */
    protected $unitType;
    /**
     * @var int
     */
    protected $unitCount;
    /**
     * @var int[]
     */
    protected $unitsCount;
    /**
     * @var string
     */
    protected $currency;
    /**
     * @var float
     */
    protected $amount;
    /**
     * @var \DateTime
     */
    protected $deadline;
    /**
     * @var string
     */
    protected $paymentProvider;
    /**
     * @var \DateTime
     */
    protected $created;
    /**
     * @var \DateTime
     */
    protected $started;
    /**
     * @var \DateTime
     */
    protected $delivered;
    /**
     * @var int
     */
    protected $progress;
    /**
     * @var string
     */
    protected $status;
    /**
     * @var bool
     */
    protected $approvalRequired;
    /**
     * @var \DateTime
     */
    protected $deleted;
    /**
     * @var bool
     */
    protected $isDeleted;
    /**
     * @var OrderStatisticsViewModel
     */
    protected $statistics;
    /**
     * @return TranslationViewModel[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }
    /**
     * @param TranslationViewModel[] $translations
     *
     * @return self
     */
    public function setTranslations(array $translations = null)
    {
        $this->translations = $translations;
        return $this;
    }
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
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }
    /**
     * @param int $number
     *
     * @return self
     */
    public function setNumber($number = null)
    {
        $this->number = $number;
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
     * @return string[]
     */
    public function getTo()
    {
        return $this->to;
    }
    /**
     * @param string[] $to
     *
     * @return self
     */
    public function setTo(array $to = null)
    {
        $this->to = $to;
        return $this;
    }
    /**
     * @return bool
     */
    public function getIsLayoutRequired()
    {
        return $this->isLayoutRequired;
    }
    /**
     * @param bool $isLayoutRequired
     *
     * @return self
     */
    public function setIsLayoutRequired($isLayoutRequired = null)
    {
        $this->isLayoutRequired = $isLayoutRequired;
        return $this;
    }
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
    /**
     * @param string $label
     *
     * @return self
     */
    public function setLabel($label = null)
    {
        $this->label = $label;
        return $this;
    }
    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }
    /**
     * @param string $paymentType
     *
     * @return self
     */
    public function setPaymentType($paymentType = null)
    {
        $this->paymentType = $paymentType;
        return $this;
    }
    /**
     * @return string
     */
    public function getUnitType()
    {
        return $this->unitType;
    }
    /**
     * @param string $unitType
     *
     * @return self
     */
    public function setUnitType($unitType = null)
    {
        $this->unitType = $unitType;
        return $this;
    }
    /**
     * @return int
     */
    public function getUnitCount()
    {
        return $this->unitCount;
    }
    /**
     * @param int $unitCount
     *
     * @return self
     */
    public function setUnitCount($unitCount = null)
    {
        $this->unitCount = $unitCount;
        return $this;
    }
    /**
     * @return int[]
     */
    public function getUnitsCount()
    {
        return $this->unitsCount;
    }
    /**
     * @param int[] $unitsCount
     *
     * @return self
     */
    public function setUnitsCount(\ArrayObject $unitsCount = null)
    {
        $this->unitsCount = $unitsCount;
        return $this;
    }
    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
    /**
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency($currency = null)
    {
        $this->currency = $currency;
        return $this;
    }
    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }
    /**
     * @param float $amount
     *
     * @return self
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }
    /**
     * @param \DateTime $deadline
     *
     * @return self
     */
    public function setDeadline(\DateTime $deadline = null)
    {
        $this->deadline = $deadline;
        return $this;
    }
    /**
     * @return string
     */
    public function getPaymentProvider()
    {
        return $this->paymentProvider;
    }
    /**
     * @param string $paymentProvider
     *
     * @return self
     */
    public function setPaymentProvider($paymentProvider = null)
    {
        $this->paymentProvider = $paymentProvider;
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
    /**
     * @return \DateTime
     */
    public function getStarted()
    {
        return $this->started;
    }
    /**
     * @param \DateTime $started
     *
     * @return self
     */
    public function setStarted(\DateTime $started = null)
    {
        $this->started = $started;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getDelivered()
    {
        return $this->delivered;
    }
    /**
     * @param \DateTime $delivered
     *
     * @return self
     */
    public function setDelivered(\DateTime $delivered = null)
    {
        $this->delivered = $delivered;
        return $this;
    }
    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }
    /**
     * @param int $progress
     *
     * @return self
     */
    public function setProgress($progress = null)
    {
        $this->progress = $progress;
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
     * @return bool
     */
    public function getApprovalRequired()
    {
        return $this->approvalRequired;
    }
    /**
     * @param bool $approvalRequired
     *
     * @return self
     */
    public function setApprovalRequired($approvalRequired = null)
    {
        $this->approvalRequired = $approvalRequired;
        return $this;
    }
    /**
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
    /**
     * @param \DateTime $deleted
     *
     * @return self
     */
    public function setDeleted(\DateTime $deleted = null)
    {
        $this->deleted = $deleted;
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
     * @return OrderStatisticsViewModel
     */
    public function getStatistics()
    {
        return $this->statistics;
    }
    /**
     * @param OrderStatisticsViewModel $statistics
     *
     * @return self
     */
    public function setStatistics(OrderStatisticsViewModel $statistics = null)
    {
        $this->statistics = $statistics;
        return $this;
    }
}