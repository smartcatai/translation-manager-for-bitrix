<?php
namespace Smartcat\Connector;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class SmartcatTaskTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_IBLOCK_ID int mandatory
 * <li> ELEMENT_ID int mandatory
 * <li> TARGET_ELEMENT_ID int optional
 * <li> STATUS unknown mandatory default 'N'
 * </ul>
 *
 * @package Bitrix\Likee
 **/
class TaskTable extends Main\Entity\DataManager
{

    const STATUS_NEW = 'N';
    const STATUS_UPLOADED = 'U';
    const STATUS_PROCESS = 'P';
    const STATUS_FAILED = 'F';
    const STATUS_SUCCESS = 'S';
    const STATUS_CANCELED = 'C';

    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_STATUS_NEW'),
            self::STATUS_UPLOADED => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_STATUS_UPLOADED'),
            self::STATUS_PROCESS => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_STATUS_PROCESS'),
            self::STATUS_FAILED => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_STATUS_FAILED'),
            self::STATUS_SUCCESS => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_STATUS_SUCCESS'),
            self::STATUS_CANCELED => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_STATUS_CANCELED'),
        ];
    }

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_smartcat_connector_task';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_ID_FIELD'),
            ),
            'PROFILE_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_PROFILE_ID_FIELD'),
            ),
            'ELEMENT_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_ELEMENT_ID_FIELD'),
            ),
            'STATUS' => array(
                'data_type' => 'string',
                //'validation' => array(__CLASS__, 'validateStatus'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_STATUS_FIELD'),
                'default_value' => self::STATUS_NEW,
            ),
            'ORDER_ID' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateOrderId'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_ORDER_ID_FIELD'),
            ),
            'ORDER_NUMBER' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateOrderNumber'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_ORDER_NUMBER_FIELD'),
            ),
            'DATE_CREATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_DATE_CREATE_FIELD'),
                'default_value' => new Main\Type\DateTime(),
            ),
            'DEADLINE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_DEADLINE_FIELD'),
                'default_value' => new Main\Type\DateTime(),
            ),
            'DATE_UPDATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_DATE_UPDATE_FIELD'),
                'default_value' => new Main\Type\DateTime(),
            ),
            'CONTENT' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_CONTENT_FIELD'),
            ),
            'FILE_ID' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateFileId'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_FILE_ID_FIELD'),
            ),
            'FILE_TOKEN' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateFileToken'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_FILE_TOKEN_FIELD'),
            ),
            'COMMENT' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_COMMENT_FIELD'),
            ),
            'AMOUNT' => array(
                'data_type' => 'float',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_AMOUNT_FIELD'),
            ),
            'CURRENCY' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateCurrency'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_CURRENCY_FIELD'),
            ),
            'VENDOR' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateVendor'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_TYPE_FIELD'),
            ),
        );
    }

    public static function onBeforeUpdate(Main\Entity\Event $event)
    {
        $result = new  Main\Entity\EventResult;

        $result->modifyFields([
            'DATE_UPDATE' => new Main\Type\DateTime(),
        ]);

        return $result;
    }

    public static function OnBeforeDelete(Main\Entity\Event $event)
    {
        $result = new  Main\Entity\EventResult;
        $id = $event->getParameter("primary");
        if ($id > 0) {
            $rsFiles = TaskFileTable::getList([
                'filter' => [
                    '=TASK_ID' => $id,
                ],
            ]);

            while ($arFile = $rsFiles->fetch()) {
                TaskFileTable::delete($arFile['ID']);
            }
        }
        return $result;
    }

    /**
     * Returns validators for LANG field.
     *
     * @return array
     */
    public static function validateStatus()
    {
        return array(
            new Main\Entity\Validator\Enum([
                self::STATUS_NEW,
                self::STATUS_UPLOADED,
                self::STATUS_PROCESS,
                self::STATUS_FAILED,
                self::STATUS_SUCCESS,
            ])
        );
    }

    /**
     * Returns validators for ORDER_ID field.
     *
     * @return array
     */
    public static function validateOrderId()
    {
        return array(
            new Main\Entity\Validator\Length(null, 100),
        );
    }

    /**
     * Returns validators for ORDER_NUMBER field.
     *
     * @return array
     */
    public static function validateOrderNumber()
    {
        return array(
            new Main\Entity\Validator\Length(null, 100),
        );
    }

    /**
     * Returns validators for FILE_ID field.
     *
     * @return array
     */
    public static function validateFileId()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }

    /**
     * Returns validators for FILE_TOKEN field.
     *
     * @return array
     */
    public static function validateFileToken()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }

    /**
     * Returns validators for CURRENCY field.
     *
     * @return array
     */
    public static function validateCurrency()
    {
        return array(
            new Main\Entity\Validator\Length(null, 5),
        );
    }

    /**
     * Returns validators for HT_TYPE field.
     *
     * @return array
     */
    public static function validateVendor()
    {
        return array(
            new Main\Entity\Validator\Length(null, 100),
        );
    }
}