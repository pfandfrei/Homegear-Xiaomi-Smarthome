<?php
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
    }
    
    public function updateEvent($hg, $event)
    {
        parent::updateEvent($hg, $event);
    }
}