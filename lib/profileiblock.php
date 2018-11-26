<?php
namespace Smartcat\Connector;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class SmartcatProfileIblockTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> IBLOCK_ID_FROM int mandatory
 * <li> IBLOCK_ID_TO int mandatory
 * <li> LANG_FROM string(2) mandatory
 * <li> LANG_TO string(2) mandatory
 * <li> FIELDS string optional
 * </ul>
 *
 * @package Bitrix\Likee
 **/

class ProfileIblockTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_smartcat_connector_profile_iblock';
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
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_IBLOCK_ENTITY_ID_FIELD'),
            ),
            'PROFILE_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_IBLOCK_ENTITY_PROFILE_ID_FIELD'),
            ),
            'IBLOCK_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_IBLOCK_ENTITY_IBLOCK_ID_FIELD'),
            ),
            'LANG' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLang'),
                'title' => Loc::getMessage('SMARTCAT_CONNECTOR_PROFILE_IBLOCK_ENTITY_LANG_FIELD'),
            ),

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
            new Main\Entity\Validator\Length(null, 10),
        );
    }
}