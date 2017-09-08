<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiMagnet extends MiBaseDevice
{
    private $_type_id;
    
    const STATE_OPEN = 0;
    const STATE_CLOSED = 1;
    const STATE_UNKNOWN = 2;
    
    public function __construct($config, $model)
    {
        $this->_model = $model;
        switch ($model)
        {
            case MiConstants::MODEL_MAGNET:
                $this->_type_id = 0x287a;
                break;
            case MiConstants::MODEL_MAGNET_AQ2:
                $this->_type_id = 0x2879;
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
                case 'unknown':
                    $hg->setValue($this->_peerId, 1, 'STATE', MiMagnet::STATE_UNKNOWN);
                    break;
                case 'close':
                    $hg->setValue($this->_peerId, 1, 'STATE', MiMagnet::STATE_CLOSED);
                    break;
                case 'open':
                    $hg->setValue($this->_peerId, 1, 'STATE', MiMagnet::STATE_OPEN);
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