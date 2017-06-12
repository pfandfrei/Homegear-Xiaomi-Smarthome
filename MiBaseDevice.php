<?php

abstract class MiBaseDevice extends Threaded
{
    protected $_sid;
    public $_peerId;
    protected $_model;
    protected $_voltage;
    
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
        if ($this->setProperty($data, 'voltage'))
        {
            $hg->setValue($this->_peerId, 0, 'VOLTAGE', $data->voltage);   
        }
    }
    
    public function updateEvent($hg, $event)
    {
        // nothing to do yet
    }
    
    protected function setProperty($mixed, $property)
    {
        $result = FALSE;
        if (property_exists($mixed, $property))
        {
            $this->{'_'.$property} = $mixed->$property;
            $result = TRUE;
        }
        
        return $result;
    }
    
    public function run() { }
}
