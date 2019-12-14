<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


include_once 'MiConstants.php';
include_once 'MiBaseDevice.php';

class MiGenericSwitch extends MiBaseDevice
{    
    private $_channels;
    private $_type_id;
    
    public function __construct($config, $model)
    {
        $this->_model = $model;
        switch ($model)
        {
            case MiConstants::MODEL_CTRL_NEUTRAL1:
            case MiConstants::MODEL_CTRL_NEUTRAL1_AQ1:
                $this->_type_id = 0x28c0;
                $this->_channels = 1;
                break;
            case MiConstants::MODEL_86SW1:
            case MiConstants::MODEL_SENSOR_86SW1:
            case MiConstants::MODEL_SENSOR_86SW1_AQ1:
                $this->_type_id = 0x28c1;
                $this->_channels = 1;
                break;
            case MiConstants::MODEL_CTRL_LN1:
            case MiConstants::MODEL_CTRL_LN1_AQ1:
                $this->_type_id = 0x28c2;
                $this->_channels = 1;
                break;
            case MiConstants::MODEL_CTRL_NEUTRAL2:
            case MiConstants::MODEL_CTRL_NEUTRAL2_AQ1:
                $this->_type_id = 0x28d0;
                $this->_channels = 2;
                break;
            case MiConstants::MODEL_86SW2:
            case MiConstants::MODEL_SENSOR_86SW2:
            case MiConstants::MODEL_SENSOR_86SW2_AQ1:
            case MiConstants::MODEL_REMOTE_B286ACN01:
                $this->_type_id = 0x28d1;
                $this->_channels = 2;
                break;
            case MiConstants::MODEL_CTRL_LN2:
            case MiConstants::MODEL_CTRL_LN2_AQ1:
                $this->_type_id = 0x28d2;
                $this->_channels = 2;
                break;
            default:
                $this->_model = MiConstants::MODEL_UNKNOWN;
                $this->_channels = 0;
                $this->_type_id = 0x0000;
        }
        parent::__construct($config);        
    }
    
    public function getTypeId() { return $this->_type_id; }
    
    public function updateData($hg, $data)
    {
        parent::updateData($hg, $data);
        
        if (property_exists($data, 'dual_channel'))
        {
            if ($data->dual_channel == 'both_click')
            {
                $hg->setValue($this->_peerId, $this->_channels+1, 'PRESS_BOTH', TRUE);
            }
        }
        else
        {
            for ($i=0; $i<$this->_channels; $i++)
            {
                $channel = 'channel_'.$i;
                if (property_exists($data, $channel))
                {
                    switch ($data->{$channel})
                    {
                        case 'click':
                            $hg->setValue($this->_peerId, $i+1, 'PRESS_SHORT', TRUE);
                            break;
                        case 'long_click_press':
                            $hg->setValue($this->_peerId, $i+1, 'PRESS_LONG', TRUE);
                            break;
                        case 'long_click_release':
                            $hg->setValue($this->_peerId, $i+1, 'PRESS_LONG_RELEASE', TRUE);
                            break;
                        case 'double_click':
                            $hg->setValue($this->_peerId, $i+1, 'PRESS_DOUBLE', TRUE);
                            break;
                        case 'both_click':
                            $hg->setValue($this->_peerId, $i+1, 'PRESS_BOTH', TRUE);
                            break;
                        case 'on':
                            $hg->setValue($this->_peerId, $i+1, 'STATE', TRUE);
                            break;
                        case 'off':
                            $hg->setValue($this->_peerId, $i+1, 'STATE', FALSE);
                            break;
                        default:
                            break;
                    }
                }
            }            
        }
    }
    
    public function updateEvent($hg, $event)
    {
        $result = FALSE;
                
        if (($event['TYPE'] == 'event')
            && ($event['PEERID'] == $this->_peerId)
            && ($event['VARIABLE'] == 'STATE'))
        {
            $result = parent::updateEvent($hg, $event);
            $channel = 'channel_'.(intval(event['PEERCHANNEL'])-1);
            $result->data->{$channel} = boolval($event['VALUE']) ? 'on' : 'off';
        }
        
        return $result;
    }
}