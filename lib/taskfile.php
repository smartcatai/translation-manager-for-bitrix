<?php
namespace Smartcat\Connector;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class TaskFileTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TASK_ID int mandatory
 * <li> ELEMENT_ID int optional
 * <li> FILE_ID string(255) optional
 * <li> FILE_TOKEN string(255) optional
 * <li> LANG_FROM string(2) mandatory
 * <li> LANG_TO string(2) mandatory
 * <li> CONTENT string optional
 * <li> TRANSLATION string optional
 * <li> STATUS unknown mandatory default 'N'
 * <li> DATE_CREATE datetime optional
 * <li> DATE_UPDATE datetime optional
 * </ul>
 *
 * @package Likee\Smartcat
 **/
class TaskFileTable extends Main\Entity\DataManager
{

    const STATUS_NEW = 'N';
    const STATUS_PROCESS = 'P';
    const STATUS_FAILED = 'F';
    const STATUS_SUCCESS = 'S';

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_smartcat_connector_task_file';
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
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_ID_FIELD'),
            ),
            'TASK_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_TASK_ID_FIELD'),
            ),
            'ELEMENT_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_ELEMENT_ID_FIELD'),
            ),

            'LANG_FROM' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLangFrom'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_LANG_FROM_FIELD'),
            ),
            'LANG_TO' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLangTo'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_LANG_TO_FIELD'),
            ),
            'DOCUMENT_ID' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateDocumentId'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_FILE_ID_FIELD'),
            ),
            'EXPORT_TASK_ID' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateExportTaskId'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_ENTITY_FILE_TOKEN_FIELD'),
            ),
            'TRANSLATION' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_TRANSLATION_FIELD'),
            ),
            'STATUS' => array(
                'data_type' => 'string',
                //'validation' => array(__CLASS__, 'validateStatus'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_STATUS_FIELD'),
                'default_value' => self::STATUS_NEW,
            ),
            'DATE_CREATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_DATE_CREATE_FIELD'),
                'default_value' => new Main\Type\DateTime(),
            ),
            'DATE_UPDATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_TASK_FILE_ENTITY_DATE_UPDATE_FIELD'),
                'default_value' => new Main\Type\DateTime(),
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

    /**
     * Returns validators for LANG_FROM field.
     *
     * @return array
     */
    public static function validateLangFrom()
    {
        return array(
            new Main\Entity\Validator\Length(null, 10),
        );
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
                self::STATUS_PROCESS,
                self::STATUS_FAILED,
                self::STATUS_SUCCESS,
            ])
        );
    }

    /**
     * Returns validators for LANG_TO field.
     *
     * @return array
     */
    public static function validateLangTo()
    {
        return array(
            new Main\Entity\Validator\Length(null, 10),
        );
    }

    /**
     * Returns validators for FILE_ID field.
     *
     * @return array
     */
    public static function validateDocumentId()
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
    public static function validateExportTaskId()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
}