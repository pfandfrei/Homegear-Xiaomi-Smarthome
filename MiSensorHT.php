<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiSensorHT extends MiBaseDevice
{
    const TYPE_ID = 0x288c;
    
    protected $_temperature;
    protected $_humidity;
    
    public function __construct($data)
    {
        $this->_model = MiConstants::MODEL_SENSOR_HT;
        parent::__construct($data);   
        $this->setProperty($data, 'temperature');
        $this->setProperty($data, 'humidity');
    }    
    
    public function getTypeId() { return MiSensorHT::TYPE_ID; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);

        if ($this->setProperty($data, 'temperature'))
        {
            $hg->setValue($this->_peerId, 1, 'TEMPERATURE', intval($data->temperature)/100.0);   
        }
        if ($this->setProperty($data, 'humidity'))
        {
            $hg->setValue($this->_peerId, 1, 'HUMIDITY', intval($data->humidity)/100.0);   
        }
    }
    
    public function getTemperature()
    {
        return $this->_temperature / 100.0;
    }
    
    public function getHumidity()
    {
        return $this->_humidity / 100.0;
    }
}