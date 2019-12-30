<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */
include_once 'MiLogger.php';

abstract class MiBaseDevice extends Threaded
{
    protected $_sid;
    public $_peerId;
    protected $_model;
    protected $_voltage;
    protected $_heartbeat_timeout = 60 * 60;
    
    public function __construct($data)
    {
        $this->setProperty($data, 'voltage');
    }
    
    public abstract function getTypeId();
    
    public function getPeerId() { return $this->_peerId; }
    
    public function setPeerId($peerId) { $this->_peerId = $peerId; }
    
    public function getSid() { return $this->_sid; }
    
    public function getModel() { return $this->_model; }
    
    public function updateData($hg, $data)
    {
        try
        {
            if ($this->setProperty($data, 'voltage'))
            {
                $this->_voltage = intval($data->voltage);
                $hg->setValue($this->_peerId, 0, 'VOLTAGE', $this->_voltage);  
                $hg->setValue($this->_peerId, 0, 'LOWBAT', $this->_voltage<2800);   
            }
            // finally update heartbeat timestamp
            $hg->setValue($this->_peerId, 0, 'HEARTBEAT', time()); 
        }
        catch (\Homegear\HomegearException $e)
        {
            MiLogger::Instance()->exception_log($e, $this->_model, $this->_type);
        }
        catch (Exception $e)
        {
            MiLogger::Instance()->exception_log($e, $this->_model);
        }
    }
    
    public function updateEvent($hg, $event)
    {
        $result = new stdClass();
        $result->cmd = MiConstants::CMD_WRITE;
        $result->model = $this->_model;
        $result->sid = $this->_sid;
        $result->short_id = 0;
        $result->data = new stdClass();
        return $result;
    }
    
    protected function setProperty($mixed, $property)
    {
        $result = FALSE;
        try
        {
            if (property_exists($mixed, $property))
            {
                $this->{'_'.$property} = $mixed->$property;
                $result = TRUE;
            }
        }
        catch (\Homegear\HomegearException $e)
        {
            MiLogger::Instance()->exception_log($e);
        }
        catch (Exception $e)
        {
            MiLogger::Instance()->exception_log($e);
        }
        
        return $result;
    }
    
    public function run() { }
}
