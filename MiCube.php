<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';

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
            $rotate = json_decode($data->rotate);
    $now = strftime('%Y-%m-%d %H:%M:%S');
    error_log($now . ' >>  ' . $rotate . PHP_EOL, 3, MiConstants::LOGFILE);
            list($angle, $time) = explode(',', $rotate);
            $hg->setValue($this->_peerId, 2, 'ROTATE', intval($angle));
            $hg->setValue($this->_peerId, 2, 'TIME', intval($time));
            if (intval($angle) < 0)
            {
                $hg->setValue($this->_peerId, 2, 'ROTATE_LEFT', TRUE);
            }
            else
            {
                $hg->setValue($this->_peerId, 2, 'ROTATE_RIGHT', TRUE);                
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