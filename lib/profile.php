<?php
namespace Smartcat\Connector;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class ProfileTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(255) mandatory
 * <li> ACTIVE unknown mandatory default 'N'
 * </ul>
 *
 * @package Likee\Smartcat
 **/
class ProfileTable extends Main\Entity\DataManager
{

    public static function getTypeList()
    {
        return [
            'mt' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_MT'),
            'ht_express' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_HT_EXPRESS'),
            'ht_expert' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_HT_EXPERT'),
            'ht_professional' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_HT_PROFESSIONAL'),
        ];
    }

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_smartcat_connector_profile';
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
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_ID_FIELD'),
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateName'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_NAME_FIELD'),
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_ACTIVE_FIELD'),
            ),
            'PUBLISH' => array(
                'data_type' => 'boolean',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_PUBLISH_FIELD'),
            ),
            'AUTO_ORDER' => array(
                'data_type' => 'boolean',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_AUTO_ORDER_FIELD'),
            ),
            'IBLOCK_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_IBLOCK_ID_FIELD'),
            ),
            'LANG' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLang'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_LANG_FIELD'),
            ),
            'FIELDS' => array(
                'data_type' => 'text',
                'serialized' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_IBLOCK_ENTITY_FIELDS_FIELD'),
            ),
            'WORKFLOW' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateType'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_WORKFLOW_FIELD'),
            ),
            'VENDOR' => array(
                'data_type' => 'text',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_VENDOR_FIELD'),
            ),
        );
    }

    /**
     * Returns validators for NAME field.
     *
     * @return array
     */
    public static function validateName()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }

    /**
     * Returns validators for LANG field.
     *
     * @return array
     */
    public static function validateLang()
    {
        return array(
            new Main\Entity\Validator\Length(null, 5),
        );
    }

    /**
     * Returns validators for TYPE field.
     *
     * @return array
     */
    public static function validateType()
    {
        return array(
            new Main\Entity\Validator\Length(null, 100),
        );
    }

    public static function onBeforeDelete(Main\Entity\Event $event)
    {
        $result = new Main\Entity\EventResult;
        $id = $event->getParameter("primary");

        if ($id > 0) {
            $rsIBlocks = ProfileIblockTable::getList([
                'filter' => [
                    '=PROFILE_ID' => $id,
                ],
            ]);
            while ($arIBlock = $rsIBlocks->fetch()) {
                ProfileIblockTable::delete($arIBlock['ID']);
            }

            $rsTasks = TaskTable::getList([
                'filter' => [
                    '=PROFILE_ID' => $id,
                ],
            ]);
            while ($arTask = $rsTasks->fetch()) {
                TaskTable::delete($arTask['ID']);
            }

        }

        return $result;
    }
}