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
use Bitrix\Main\Entity\Validator\Length as LengthValidator;

Loc::loadMessages(__FILE__);

class ProfileIblockTable extends DataManager
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
            new LengthValidator(null, 10),
        );
    }
}