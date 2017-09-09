<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiMotion extends MiBaseDevice
{
    private $_type_id;
    
    protected $_lux;
    
    public function __construct($config, $model)
    {
        $this->_model = $model;
        switch ($model)
        {
            case MiConstants::MODEL_MOTION:
                $this->_type_id = 0x287b;
                break;
            case MiConstants::MODEL_MOTION_AQ2:
                $this->_type_id = 0x287e;
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
                case 'motion':
                    $hg->setValue($this->_peerId, 1, 'MOTION', TRUE);
                    break;
            }
        }
        if ($this->setProperty($data, 'lux'))
        {
            $hg->setValue($this->_peerId, 1, 'ILLUMINATION', $data->lux); 
        }
    }
    
    public function getIllumination()
    {
        return $this->_illumination;
    }
    
    public function updateEvent($hg, $event)
    {
        //parent::updateEvent($hg, $event);
        return FALSE;
    }
}