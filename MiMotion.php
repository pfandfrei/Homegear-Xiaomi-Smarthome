<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';

class MiMotion extends MiBaseDevice
{
    const TYPE_ID = 0x287b;
    
    public function __construct($config)
    {
        $this->_model = MiConstants::MODEL_MOTION;
        parent::__construct($config);        
    }
    
    public function getTypeId() { return MiMotion::TYPE_ID; }
    
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
    }
    
    public function updateEvent($hg, $event)
    {
        //parent::updateEvent($hg, $event);
        return FALSE;
    }
}