<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiGenericAlarm extends MiBaseDevice
{
    private $_type_id;
    private $_alarm;
    
    public function __construct($config, $model)
    {
        $this->_model = $model;
        switch ($model)
        {
            case MiConstants::MODEL_SMOKE:
                $this->_type_id = 0x28e0;
                break;
            case MiConstants::MODEL_NATGAS:
                $this->_type_id = 0x28e1;
                break;
            default:
                $this->_model = MiConstants::MODEL_UNKNOWN;
                break;
        }
        
        parent::__construct($config);        
    }
    
    public function getTypeId() { return $this->_type_id; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if ($this->setProperty($data, 'alarm'))
        {
            $hg->setValue($this->_peerId, 1, 'ALARM', intval($data->alarm));
        }
    }
    
    public function updateEvent($hg, $event)
    {
        parent::updateEvent($hg, $event);
        return FALSE;
    }
}