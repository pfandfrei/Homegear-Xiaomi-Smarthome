<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';

class MiMagnet extends MiBaseDevice
{
    const TYPE_ID = 0x287a;
    
    public function __construct($config)
    {
        $this->_model = MiConstants::MODEL_MAGNET;
        parent::__construct($config);        
    }
    
    public function getTypeId() { return MiMagnet::TYPE_ID; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if (property_exists($data, 'status'))
        {
            switch ($data->status)
            {
                case 'unknown':
                    $hg->setValue($this->_peerId, 1, 'STATE', 0);
                    break;
                case 'closed':
                    $hg->setValue($this->_peerId, 1, 'STATE', 1);
                    break;
                case 'open':
                    $hg->setValue($this->_peerId, 1, 'STATE', 2);
                    break;
            }
        }
    }
    
    public function updateEvent($hg, $event)
    {
        parent::updateEvent($hg, $event);
    }
}