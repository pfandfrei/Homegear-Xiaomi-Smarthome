<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiSwitch extends MiBaseDevice
{
    private $_type_id;
    
    public function __construct($config, $model)
    {
        $this->_model = $model;
        switch ($model)
        {
            case MiConstants::MODEL_SWITCH:
                $this->_type_id = 0x287c;
                break;
            case MiConstants::MODEL_SWITCH_AQ2:
                $this->_type_id = 0x287d;
                break;
            case MiConstants::MODEL_SWITCH_AQ3:
                $this->_type_id = 0x2878;
                break;
            default:
                $this->_model = MiConstants::MODEL_UNKNOWN;
        }
        parent::__construct($config);              
    }
    
    public function getTypeId() { return $this->_type_id; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if (property_exists($data, 'status'))
        {
            switch ($data->status)
            {
                case 'click':
                    $hg->setValue($this->_peerId, 1, 'PRESS_SHORT', TRUE);
                    break;
                case 'long_click_press':
                    $hg->setValue($this->_peerId, 1, 'PRESS_LONG', TRUE);
                    break;
                case 'long_click_release':
                    $hg->setValue($this->_peerId, 1, 'PRESS_LONG_RELEASE', TRUE);
                    break;
                case 'double_click':
                    $hg->setValue($this->_peerId, 1, 'PRESS_DOUBLE', TRUE);
                    break;
                case 'shake':
                    $hg->setValue($this->_peerId, 1, 'SHAKE', TRUE);
                    break;
                default:
                    break;
            }
        }
    }
    
    public function updateEvent($hg, $event)
    {
        //parent::updateEvent($hg, $event);
        return FALSE;
    }
}