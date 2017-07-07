<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';

class MiMagnet extends MiBaseDevice
{
    const TYPE_ID = 0x287a;
    
    const STATE_UNKNOWN = 0;
    const STATE_CLOSED = 1;
    const STATE_OPEN = 2;
    
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