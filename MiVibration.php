<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2018
 */

include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiVibration extends MiBaseDevice
{   
    const TYPE_ID = 0x2877;
    
    public function __construct($data)
    {
        $this->_model = MiConstants::MODEL_VIBRATION;
    }    
    
    public function getTypeId() { return MiVibration::TYPE_ID; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if (property_exists($data, 'status'))
        {
            switch ($data->status)
            {
                case 'vibrate':
                    $hg->setValue($this->_peerId, 1, 'VIBRATE', TRUE);
                    break;
                case 'free_fall':
                    $hg->setValue($this->_peerId, 1, 'FREE_FALL', TRUE);
                    break;
                case 'tilt':
                    $hg->setValue($this->_peerId, 1, 'TILT', TRUE);
                    break;
            }
        }
        if (property_exists($data, 'coordination'))
        {
            $values = explode(",", $data->coordination);
            if (count($values) == 3)
            {
                $hg->setValue($this->_peerId, 2, 'X', intval($values[0]));
                $hg->setValue($this->_peerId, 2, 'Y', intval($values[1]));
                $hg->setValue($this->_peerId, 2, 'Z', intval($values[2]));
            }
        }
        if (property_exists($data, 'bed_activity'))
        {
            // {"cmd":"report","model":"vibration","sid":"158d0002a2225b","short_id":48333,"data":"{\"bed_activity\":\"120\"}"}
            $hg->setValue($this->_peerId, 3, 'BED_ACTIVITY', intval($data->bed_activity));
        }
        if (property_exists($data, 'final_tilt_angle'))
        {
            // {"cmd":"report","model":"vibration","sid":"158d0002a2225b","short_id":48333,"data":"{\"final_tilt_angle\":\"179\"}"}
            $hg->setValue($this->_peerId, 3, 'FINAL_TILT_ANGLE', intval($data->final_tilt_angle));
        }
    }
    
    public function updateEvent($hg, $event)
    {
        //parent::updateEvent($hg, $event);
        return FALSE;
    }
}