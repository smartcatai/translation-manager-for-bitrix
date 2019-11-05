<?php
/**
 * @package    Smartcat Translation Manager for Bitrix
 *
 * @author     Smartcat <support@smartcat.ai>
 * @copyright  (c) 2019 Smartcat. All Rights Reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://smartcat.ai
 */

namespace Smartcat\Connector\Models;

use Smartcat\Connector\Tables\ProfileTable;

class ProfileModel extends AbstractModel
{
    const ID = 'ID';
    const NAME = 'NAME';
    const ACTIVE = 'ACTIVE';
    const PUBLISH = 'PUBLISH';
    const AUTO_ORDER = 'AUTO_ORDER';
    const IBLOCK_ID = 'IBLOCK_ID';
    const PROJECT_ID = 'PROJECT_ID';
    const LANG = 'LANG';
    const FIELDS = 'FIELDS';
    const WORKFLOW = 'WORKFLOW';
    const VENDOR = 'VENDOR';

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var bool */
    protected $isActive;
    /** @var bool */
    protected $isPublished;
    /** @var bool */
    protected $isAutoOrder;
    /** @var int */
    protected $iblockId;
    /** @var int */
    protected $projectId;
    /** @var string */
    protected $language;
    /** @var string */
    protected $fields;
    /** @var string */
    protected $workflow;
    /** @var string */
    protected $vendor;


    /**
     * @return array
     */
    public function attributes()
    {
        return [
            self::ID => 'id',
            self::NAME => 'name',
            self::ACTIVE => 'isActive',
            self::PUBLISH => 'isPublished',
            self::AUTO_ORDER => 'isAutoOrder',
            self::IBLOCK_ID => 'iblockId',
            self::PROJECT_ID => 'projectId',
            self::LANG => 'language',
            self::FIELDS => 'fields',
            self::WORKFLOW => 'workflow',
            self::VENDOR => 'vendor',
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->isPublished;
    }

    /**
     * @param bool $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return bool
     */
    public function isAutoOrder()
    {
        return $this->isAutoOrder;
    }

    /**
     * @param bool $isAutoOrder
     */
    public function setIsAutoOrder($isAutoOrder)
    {
        $this->isAutoOrder = $isAutoOrder;
    }

    /**
     * @return int
     */
    public function getIblockId()
    {
        return $this->iblockId;
    }

    /**
     * @param int $iblockId
     */
    public function setIblockId($iblockId)
    {
        $this->iblockId = $iblockId;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
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
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @param string $workflow
     */
    public function setWorkflow($workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return bool
     */
    public function update()
    {
        try {
            return ProfileTable::update($this->id, $this->getDiff())->isSuccess();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function insert()
    {
        try {
            return ProfileTable::add($this->toArray())->isSuccess();
        } catch (\Exception $e) {
            return false;
        }
    }
}