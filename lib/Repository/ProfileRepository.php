<?php
/**
 * @package    Smartcat Translation Manager for Bitrix
 *
 * @author     Smartcat <support@smartcat.ai>
 * @copyright  (c) 2019 Smartcat. All Rights Reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://smartcat.ai
 */

namespace Smartcat\Connector\Repository;

use Smartcat\Connector\Models\ProfileModel;
use Smartcat\Connector\Tables\ProfileTable;

class ProfileRepository
{
    /**
     * @param $IblockId
     * @return ProfileModel[]
     */
    public static function getAllByIblockId($IblockId)
    {
        try {
            $profiles = ProfileTable::getList([
                'filter' => [
                    '=IBLOCK_ID' => intval($IblockId),
                    '=ACTIVE' => 'Y',
                    '=AUTO_ORDER' => 'Y',
                ],
            ])->fetchAll();

            foreach ($profiles as $key => &$profile) {
                $profile = new ProfileModel($profile);
            }

            return $profiles;
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * @param $profileId
     * @return ProfileModel
     */
    public static function getOneById($profileId)
    {
        try {
            return new ProfileModel(
                ProfileTable::getById(intval($profileId))->fetch()
            );
        } catch (\Exception $e) {
            return new ProfileModel();
        }
    }
}