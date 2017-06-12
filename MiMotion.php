<?php
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
    }
    
    public function updateEvent($hg, $event)
    {
        parent::updateEvent($hg, $event);
    }
}