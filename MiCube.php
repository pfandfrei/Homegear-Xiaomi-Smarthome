<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiCube extends MiBaseDevice
{
    const TYPE_ID = 0x28bc;
    
    public function __construct($config)
    {
        $this->_model = MiConstants::MODEL_CUBE;
        parent::__construct($config);        
    }
    
    public function getTypeId() { return MiCube::TYPE_ID; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if (property_exists($data, 'rotate'))
        {
            $args = explode(',', $data->rotate);
            $angle = $args[0];
            $hg->setValue($this->_peerId, 2, 'ROTATE', intval($angle));
            if (intval($angle) < 0)
            {
                $hg->setValue($this->_peerId, 2, 'ROTATE_LEFT', TRUE);
            }
            else
            {
                $hg->setValue($this->_peerId, 2, 'ROTATE_RIGHT', TRUE);                
            }
            if (count($args>1))
            {
                $hg->setValue($this->_peerId, 2, 'TIME', intval($args[1]));
            }
        }
        if (property_exists($data, 'status'))
        {
            $status = json_decode($data->status);
            switch ($status)
            {
                case 'flip90':
                    $hg->setValue($this->_peerId, 1, 'FLIP90', TRUE);
                    break;
                case 'flip180':
                    $hg->setValue($this->_peerId, 1, 'FLIP180', TRUE);
                    break;
                case 'move':
                    $hg->setValue($this->_peerId, 1, 'MOVE', TRUE);
                    break;
                case 'tap_twice':
                    $hg->setValue($this->_peerId, 1, 'TAP_TWICE', TRUE);
                    break;
                case 'shake_air':
                    $hg->setValue($this->_peerId, 1, 'SHAKE_AIR', TRUE);
                    break;
                case 'swing':
                    $hg->setValue($this->_peerId, 1, 'SWING', TRUE);
                    break;
                case 'alert':
                    $hg->setValue($this->_peerId, 1, 'SHAKE_AIR', TRUE);
                    break;
                case 'free_fall':
                    $hg->setValue($this->_peerId, 1, 'SWING', TRUE);
                    break;
                default:
                    break;
            }
        }
    }
    
    public function updateEvent($hg, $event)
    {
        parent::updateEvent($hg, $event);
        return FALSE;
    }
}