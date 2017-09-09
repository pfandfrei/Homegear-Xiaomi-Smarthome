<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiSensorHT extends MiBaseDevice
{
    private $_type_id;
    
    protected $_temperature;
    protected $_humidity;
    protected $_pressure;
    
    public function __construct($data, $model)
    {
        $this->_model = $model;
        switch ($model)
        {;
            case MiConstants::MODEL_SENSOR_HT:
                $this->_type_id = 0x288c;
                break;
            case MiConstants::MODEL_WEATHER_V1:
                $this->_type_id = 0x288d;
                break;
            default:
                $this->_model = MiConstants::MODEL_UNKNOWN;
                break;
        }
        
        $this->setProperty($data, 'temperature');
        $this->setProperty($data, 'humidity');
        $this->setProperty($data, 'pressure');
    }    
    
    public function getTypeId() { return $this->_type_id; }
    
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
        if ($this->setProperty($data, 'pressure'))
        {
            $hg->setValue($this->_peerId, 1, 'PRESSURE', intval($data->pressure)/100.0);   
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
    
    public function getPressure()
    {
        return $this->_pressure / 100.0;
    }
    
    public function updateEvent($hg, $event)
    {
        //parent::updateEvent($hg, $event);
        return FALSE;
    }
}