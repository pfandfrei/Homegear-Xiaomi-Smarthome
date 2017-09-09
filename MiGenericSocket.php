<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiGenericSocket extends MiBaseDevice
{
    const STATE_OFF = 0;
    const STATE_ON = 1;
    const STATE_UNKNOWN = 2;
    
    private $_type_id;
    private $_status;
    private $_load_voltage;
    private $_load_power;
    private $_power_consumed;
    
    public function __construct($config, $model)
    {
        $this->_model = $model;
        switch ($model)
        {
            case MiConstants::MODEL_PLUG:
                $this->_type_id = 0x28f0;
                break;
            case MiConstants::MODEL_86PLUG:
                $this->_type_id = 0x28f1;
                break;
            default:
                $this->_model = MiConstants::MODEL_UNKNOWN;
                break;
        }
        
        parent::__construct($config);        
    }
    
    public function getTypeId() { return $this->_type_id; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if (property_exists($data, 'status'))
        {
            switch ($data->status)
            {
                case 'unknown':
                    $hg->setValue($this->_peerId, 1, 'STATE', MiGenericSocket::STATE_UNKNOWN);
                    break;
                case 'on':
                    $hg->setValue($this->_peerId, 1, 'STATE', MiGenericSocket::STATE_ON);
                    break;
                case 'off':
                    $hg->setValue($this->_peerId, 1, 'STATE', MiGenericSocket::STATE_OFF);
                    break;
            }
        }
        
        if (setProperty($data, 'load_voltage'))
        {
            $hg->setValue($this->_peerId, 1, 'LOAD_VOLTAGE', intval($data->load_voltage));
        }
        
        if (setProperty($data, 'load_power'))
        {
            $hg->setValue($this->_peerId, 1, 'LOAD_POWER', floatval($data->load_power));
        }
        
        if (setProperty($data, 'power_consumed'))
        {
            $hg->setValue($this->_peerId, 1, 'POWER_CONSUMED', floatval($data->power_consumed));
        }
    }
    
    public function updateEvent($hg, $event)
    {
        $result = FALSE;
        
        if (($event['TYPE'] == 'event')
            && ($event['PEERID'] == $this->_peerId)
            && ($event['VARIABLE'] == 'STATE'))
        {
            $new_state = intval($event['VALUE']);
            if ($new_state == MiGenericSocket::STATE_OFF || $new_state == MiGenericSocket::STATE_ON)
            {
                $state = $hg->getValue($this->_peerId, 1, 'STATE');
                if ($new_state!=$state)
                {
                    $hg->setValue($this->_peerId, 1, 'STATE', $new_state);
                    $result = parent::updateEvent($hg, $event);
                    $result->data->status =  ($new_state ? 'on' : 'off');
                }
            }
        }
        
        return $result;
    }
}