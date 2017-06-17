<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';

class MiSwitch extends MiBaseDevice
{
    const TYPE_ID = 0x287c;
    
    public function __construct($config)
    {
        $this->_model = MiConstants::MODEL_SWITCH;
        parent::__construct($config);        
    }
    
    public function getTypeId() { return MiSwitch::TYPE_ID; }
    
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
                default:
                    break;
            }
        }
    }
    
    public function updateEvent($hg, $event)
    {
        parent::updateEvent($hg, $event);
    }
}