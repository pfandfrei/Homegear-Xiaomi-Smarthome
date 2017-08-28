<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */

include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiWLeakAq1 extends MiBaseDevice
{   
    const TYPE_ID = 0x28bc;
    
    public function __construct($data)
    {
        $this->_model = MiConstants::MODEL_WLEAK_AQ1;
    }    
    
    public function getTypeId() { return MiWLeakAq1::TYPE_ID; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if (property_exists($data, 'status'))
        {
            switch ($data->status)
            {
                case 'leak':
                    $hg->setValue($this->_peerId, 1, 'LEAK', TRUE);
                    break;
                case 'no_leak':
                    $hg->setValue($this->_peerId, 1, 'LEAK', FALSE);
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