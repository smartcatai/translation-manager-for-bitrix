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

abstract class AbstractModel
{
    /**
     * @var array
     */
    protected $_oldArray = array();
    /**
     * @var bool
     */
    public $isNewRecord = true;
    /**
     * @var string
     */
    protected $primary = 'ID';

    /**
     * AbstractModel constructor.
     * @param array $fromArray
     */
    public function __construct($fromArray = array())
    {
        if (!empty($fromArray)) {
            foreach ($this->attributes() as $key => $value) {
                $this->$value = $fromArray[$key];
            }

            $this->_oldArray = $fromArray;
            $this->isNewRecord = false;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $retArray = array();

        foreach ($this->attributes() as $key => $value) {
            if ($key === $this->primary) {
                continue;
            }

            $retArray[$key] = $this->$value;
        }

        return $retArray;
    }

    /**
     * @return array
     */
    public function getDiff()
    {
        $oldArray = $this->_oldArray;
        unset($oldArray[$this->primary]);

        return array_diff_assoc($this->toArray(), $oldArray);
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->isNewRecord) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    /**
     * @return array
     */
    abstract public function attributes();

    /**
     * @return bool
     */
    abstract public function update();

    /**
     * @return bool
     */
    abstract public function insert();
}