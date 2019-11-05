<?php
/**
 * @package    Smartcat Translation Manager for Bitrix
 *
 * @author     Smartcat <support@smartcat.ai>
 * @copyright  (c) 2019 Smartcat. All Rights Reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://smartcat.ai
 */

namespace Smartcat\Connector\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\Validator\Length as LengthValidator;
use Smartcat\Connector\ProfileIblockTable;
use Smartcat\Connector\TaskTable;

Loc::loadMessages(__FILE__);

class ProfileTable extends DataManager
{
    const LEFT_TO_RIGHT = 'Y';
    const RIGHT_TO_LEFT = 'N';

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
                'values' => array(self::RIGHT_TO_LEFT, self::LEFT_TO_RIGHT),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_ACTIVE_FIELD'),
            ),
            'PUBLISH' => array(
                'data_type' => 'boolean',
                'required' => true,
                'values' => array(self::RIGHT_TO_LEFT, self::LEFT_TO_RIGHT),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_PUBLISH_FIELD'),
            ),
            'AUTO_ORDER' => array(
                'data_type' => 'boolean',
                'required' => true,
                'values' => array(self::RIGHT_TO_LEFT, self::LEFT_TO_RIGHT),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_AUTO_ORDER_FIELD'),
            ),
            'IBLOCK_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_IBLOCK_ID_FIELD'),
            ),
            'PROJECT_ID' => array(
                'data_type' => 'string',
                'required' => false,
                //'validation' => array(__CLASS__, 'validateLang'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_ENTITY_PROJECT_ID_FIELD'),
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
            new LengthValidator(null, 255),
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
            new LengthValidator(null, 5),
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
            new LengthValidator(null, 100),
        );
    }

    // TODO made this by foreign keys
    public static function onBeforeDelete(Event $event)
    {
        $result = new \Bitrix\Main\Entity\EventResult;
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