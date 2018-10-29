<?php

namespace Smartcat\ConnectorAPI\API\Model;

class SubmitOrderModel
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $contactCulture;
    /**
     * @var string
     */
    protected $contactUtcOffset;
    /**
     * @var string
     */
    protected $label;
    /**
     * @var bool
     */
    protected $approvalRequired;
    /**
     * @var bool
     */
    protected $isManualEstimation;
    /**
     * @var string
     */
    protected $costType;
    /**
     * @var string
     */
    protected $unitType;
    /**
     * @var string
     */
    protected $currency;
    /**
     * @var string
     */
    protected $from;
    /**
     * @var string[]
     */
    protected $to;
    /**
     * @var GetFileModel[]
     */
    protected $files;
    /**
     * @var string
     */
    protected $deadline;
    /**
     * @var string
     */
    protected $dead_line;
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
    public function getContactCulture()
    {
        return $this->contactCulture;
    }
    /**
     * @param string $contactCulture
     *
     * @return self
     */
    public function setContactCulture($contactCulture = null)
    {
        $this->contactCulture = $contactCulture;
        return $this;
    }
    /**
     * @return string
     */
    public function getContactUtcOffset()
    {
        return $this->contactUtcOffset;
    }
    /**
     * @param string $contactUtcOffset
     *
     * @return self
     */
    public function setContactUtcOffset($contactUtcOffset = null)
    {
        $this->contactUtcOffset = $contactUtcOffset;
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
     * @param bool $deadline
     *
     * @return self
     */
    public function setDeadline($deadline = null)
    {
        $this->dead_line = $deadline;
        $this->deadline = $deadline;
        return $this;
    }
    /**
     * @return bool
     */
    public function getDeadline()
    {
        return $this->deadline;
    }
    /**
     * @return string
     */
    public function getCostType()
    {
        return $this->costType;
    }
    /**
     * @param string $costType
     *
     * @return self
     */
    public function setCostType($costType = null)
    {
        $this->costType = $costType;
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
     * @return GetFileModel[]
     */
    public function getFiles()
    {
        return $this->files;
    }
    /**
     * @param GetFileModel[] $files
     *
     * @return self
     */
    public function setFiles(array $files = null)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsManualEstimation()
    {
        return $this->isManualEstimation;
    }

    /**
     * @param bool $isManualEstimation
     */
    public function setIsManualEstimation($isManualEstimation)
    {
        $this->isManualEstimation = $isManualEstimation;
    }


}