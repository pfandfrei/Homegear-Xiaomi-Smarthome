<?php
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
                    case 'rotate_right':
                        $hg->setValue($this->_peerId, 1, 'ROTATE_RIGHT', TRUE);
                        break;
                    case 'rotate_left':
                        $hg->setValue($this->_peerId, 1, 'ROTATE_LEFT', TRUE);
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