<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2018
 */

include_once 'MiConstants.php';
include_once 'MiCube.php';
include_once 'MiGenericAlarm.php';
include_once 'MiGenericSocket.php';
include_once 'MiGenericSwitch.php';
include_once 'MiMagnet.php';
include_once 'MiMotion.php';
include_once 'MiSensorHT.php';
include_once 'MiSwitch.php';
include_once 'MiVibration.php';
include_once 'MiWLeakAq1.php';


class StackableArray extends Threaded
{
    public function run() { }
}

class MiGateway extends Threaded
{
    const TYPE_ID = 0x286c;
    const HEARTBEAT_TIMEOUT = 10;
    
    const IV = "\x17\x99\x6d\x09\x3d\x28\xdd\xb3\xba\x69\x5a\x2e\x6f\x58\x56\x2e";
    
    private $_enable = FALSE;
    private $_rgb = 0;
    private $_illumination = 0;
    private $_mid = 10000;
    private $_vol = 0;
    private $_debug_level = 0;
    private $_proto_version = '';
    private $_password = '';
    
    private $_sid;
    private $_model;
    private $_peerId;

    private $_ip;
    private $_token;
    private $_port;
    private $_socket;
    private $_devicelist;
    private $_devices;

    public function __construct($config = null)
    {
        $this->_model = MiConstants::MODEL_GATEWAY;
        if (null != $config)
        {
            $this->_ip = $config->ip;
            $this->_port = intval($config->port);
            $this->_sid = $config->sid;
            $this->_token = '';

            $this->_devicelist = new StackableArray();
            $this->_devices = new StackableArray();

            $this->_socket = socket_create(AF_INET, SOCK_DGRAM, 0);
            socket_set_option($this->_socket, SOL_SOCKET, SO_BROADCAST, true);
            socket_set_option($this->_socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 2, 'usec' => '0'));
        }
    }

    public function __destruct()
    {
        if ($this->_socket)
        {
            socket_close($this->_socket);
            $this->_socket = null;
        }
    }
    
    public function getPeerId($sid='')
    { 
        $result = FALSE;
        if (empty($sid) || $sid == $this->_sid)
        {
            $result = $this->_peerId;
        }
        else if (array_key_exists($sid, $this->_devices))
        {
            $result = $this->_devices[$sid]->peerId;
        }
        return $result; 
    }
    
    public function setPeerId($peerId, $sid='') 
    {
        if (empty($sid) || $sid == $this->_sid)
        {
            $this->_peerId = $peerId;
        }
        else if (array_key_exists($sid, $this->_devices))
        {
            $this->_devices[$sid]->peerId = $peerId;
        }        
    }
    
    private function setProtoVersion($hg, $proto_version)
    {
        $this->_proto_version = $proto_version;
        
        $version = $hg->getVersion();
        if ($version>'Homegear 0.7')
        {
            $hg->putParamset($this->_peerId, 0, ['PROTO_VERSION' => $this->_proto_version]);
        }
    }

    public function encodeSid($sid)
    {
        $id = substr('0000000000000000'. $sid, 16);
        $address = intval(base_convert(substr($id, -16, 8), 16, 10));
        $serial = 'MI'.strtoupper(substr($id, -8));
        return [$address, $serial];
    }
    
    public function getSid() { return $this->_sid; }
    
    public function getDevicelist() { return $this->_devicelist; }
    
    public function createDevice($hg, $sid)
    {
        $peerId = 0;
        $this->addDevice($sid);
        if ($device = $this->getDevice($sid))
        {
            list($address, $serial) = $this->encodeSid($sid);
            $peerId = $hg->createDevice(MiCentral::FAMILY_ID, $device->getTypeId(), $serial, intval($address), /*protoversion*/0x0107);
            if (!$this->_oldversion)
            {
                $hg->putParamset($peerId, 0, ['SID' => $sid]);
            }
            $device->setPeerId($peerId);
        }
        return $peerId;
    }

    public function getDevice($sid)
    { 
        return array_key_exists($sid, $this->_devices) ? $result = $this->_devices[$sid] : FALSE;
    }

    public function getIpAddress()
    {
        return $this->_ip;
    }
    
    public function getParamset($hg, $channel)
    {
        if (0 == $channel)
        {
        $config = $hg->getParamset($this->_peerId, 0, 'MASTER');
        $this->_password = $config['PASSWORD'];
        $this->_debug_level = $config['DEBUG_LEVEL'] | 1;
        }
    }

    private function getGatewayKey($hg)
    {
        $encrypt = openssl_encrypt($this->_token, 'AES-128-CBC', $this->_password, OPENSSL_RAW_DATA, MiGateway::IV);
        $gateway_key = '';
        for ($i = 0; $i < 16; $i++)
        {
            $gateway_key .= sprintf('%02x', ord($encrypt[$i]));
        }

        return $gateway_key;
    }
    
    public function get_id_list($hg)
    {
        $cmd = '{"cmd": "get_id_list", sid:"' . $this->_sid . '"}';
        if (FALSE !== ($response = $this->sendCommand($this->_socket, $cmd, $this->_ip, $this->_port, MiConstants::ACK_GET_ID_LIST)))
        {
            foreach(json_decode($response->data) as $deviceid)
            {
                $this->addDevice($deviceid);
            }
        }
    }

    public function getDeviceData($hg)
    {
        if (FALSE !== ($response = $this->readDevice($this->_sid)))
        {
            $this->updateData($hg, $response);
            $data = json_decode($response->data);
            if (property_exists($data, 'proto_version') && !$this->_oldVersion)
            {
                $this->setProtoVersion($hg, $data->proto_version);
            }
        }

        foreach ($this->_devicelist as $deviceid)
        {
            $response = $this->readDevice($deviceid);
            $data = json_decode($response->data);
            $this->_devices[$deviceid]->updateData($hg, $data);
        }
    }
    
    public function readDevice($deviceid)
    {
        $cmd = '{"cmd": "read", "sid":"' . $deviceid . '"}';
        return $this->sendCommand($this->_socket, $cmd, $this->_ip, $this->_port, MiConstants::ACK_READ);
    }

    private function addDevice($deviceid)
    {
        $isKnownModel = FALSE;
        $deviceinfo = $this->readDevice($deviceid);
        $data = json_decode($deviceinfo->data);        
        if (!property_exists($data, 'error'))
        {
            $isKnownModel = TRUE;
            switch ($deviceinfo->model)
            {
                case MiConstants::MODEL_GATEWAY:
                    //$this->updateData($hg, $deviceinfo);
                    break;
                case MiConstants::MODEL_SWITCH:
                case MiConstants::MODEL_SWITCH_AQ2:
                case MiConstants::MODEL_SWITCH_AQ3:
                case MiConstants::MODEL_REMOTE_B1ACN01:
                    $this->_devices[$deviceid] = new MiSwitch($data, $deviceinfo->model);
                    break;
                case MiConstants::MODEL_SENSOR_HT:
                case MiConstants::MODEL_WEATHER_V1:
                    $this->_devices[$deviceid] = new MiSensorHT($data, $deviceinfo->model);
                    break;
                case MiConstants::MODEL_CUBE:
                case MiConstants::MODEL_SENSOR_CUBE:
                case MiConstants::MODEL_SENSOR_CUBE_AQGL01:
                    $this->_devices[$deviceid] = new MiCube($data);
                    break;
                case MiConstants::MODEL_MAGNET:
                case MiConstants::MODEL_SENSOR_MAGNET:
                case MiConstants::MODEL_SENSOR_MAGNET_AQ2:
                    $this->_devices[$deviceid] = new MiMagnet($data, $deviceinfo->model);
                    break;
                case MiConstants::MODEL_MOTION:
                case MiConstants::MODEL_SENSOR_MOTION:
                case MiConstants::MODEL_SENSOR_MOTION_AQ2:
                    $this->_devices[$deviceid] = new MiMotion($data, $deviceinfo->model);
                    break;
                case MiConstants::MODEL_CTRL_NEUTRAL1:
                case MiConstants::MODEL_CTRL_NEUTRAL1_AQ1:
                case MiConstants::MODEL_86SW1:
                case MiConstants::MODEL_SENSOR_86SW1:
                case MiConstants::MODEL_SENSOR_86SW1_AQ1:
                case MiConstants::MODEL_REMOTE_B186ACN01:
                case MiConstants::MODEL_CTRL_LN1:
                case MiConstants::MODEL_CTRL_LN1_AQ1:
                case MiConstants::MODEL_CTRL_NEUTRAL2:
                case MiConstants::MODEL_CTRL_NEUTRAL2_AQ1:
                case MiConstants::MODEL_86SW2:
                case MiConstants::MODEL_SENSOR_86SW2:
                case MiConstants::MODEL_SENSOR_86SW2_AQ1:
//                case MiConstants::MODEL_REMOTE_B286ACN01:
                case MiConstants::MODEL_CTRL_LN2:
                case MiConstants::MODEL_CTRL_LN2_AQ1:
                    $this->_devices[$deviceid] = new MiGenericSwitch($data, $deviceinfo->model);
                    break;
                case MiConstants::MODEL_SMOKE:
                case MiConstants::MODEL_SENSOR_SMOKE:
                case MiConstants::MODEL_NATGAS:
                case MiConstants::MODEL_SENSOR_NATGAS:
                    $this->_devices[$deviceid] = new MiGenericAlarm($data, $deviceinfo->model);
                    break;
                case MiConstants::MODEL_PLUG:
                case MiConstants::MODEL_86PLUG:
                case MiConstants::MODEL_CTRL_86PLUG:
                case MiConstants::MODEL_CTRL_86PLUG_AQ1:
                    $this->_devices[$deviceid] = new MiGenericSocket($data, $deviceinfo->model);
                    break;
                case MiConstants::MODEL_WLEAK_AQ1:
                    $this->_devices[$deviceid] = new MiWLeakAq1($data);
                    break;
                case MiConstants::MODEL_VIBRATION:
                case MiConstants::MODEL_LUMI_VIBRATION_AQ1:
                    $this->_devices[$deviceid] = new MiVibration($data);
                    break;
                default:
                    MiLogger::Instance()->unknown_log('unknown device: '.$deviceinfo->model);
                    $isKnownModel = FALSE;
                    break;
            }

            if ($isKnownModel)
            {
                $this->_devicelist[] = $deviceid;
            }
        }
        return $isKnownModel;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
    }
    
    public function updateEvent($hg, $event)
    {
        if ($event['TYPE'] == 'event')
        {
            if ($event['PEERID'] == $this->_peerId)
            {
                switch ($event['VARIABLE'])
                {
                    case 'ENABLE':
                        if ($event['VALUE'])
                        {
                            $rgbh = $hg->getValue($this->_peerId, 1, 'RGB_OLD');
                            $this->setRGB($hg, $rgbh);
                        }
                        else
                        {
                            $this->setRGB($hg, 0);
                        }
                        break;
                    case 'BRIGHTNESS':
                    case 'RGB':
                        $rgb = $hg->getValue($this->_peerId, 1, 'RGB');
                        $brightness = $hg->getValue($this->_peerId, 1, 'BRIGHTNESS');
                        $rgbh = $this->calcRGBH($brightness, $rgb);
                        $enabled = $hg->getValue($this->_peerId, 1, 'ENABLE');
                        $this->_rgb = $rgb;
                        $this->_brightness = $brightness;
                        $hg->setValue($this->_peerId, 1, 'RGB_OLD', $rgbh);
                        if ($enabled)
                        {
                            $this->setRGB($hg, $rgbh);
                        }
                        break;
                    case 'MUSIC_ID':
                    case 'VOLUME':
                        $this->_mid = $hg->getValue($this->_peerId, 2, 'MUSIC_ID');
                        $this->_vol = $hg->getValue($this->_peerId, 2, 'VOLUME');
                        $enabled = $hg->getValue($this->_peerId, 2, 'PLAY');
                        $hg->setValue($this->_peerId, 2, 'MUSIC_ID_OLD', $this->_mid);
                        if ($enabled)
                        {
                            $this->playMusic($hg, $this->_mid, $this->_vol);
                        }
                        break;
                    case 'PLAY':
                        if ($event['VALUE'])
                        {
                            $mid = $hg->getValue($this->_peerId, 2, 'MUSIC_ID_OLD');
                            $vol = $hg->getValue($this->_peerId, 2, 'VOLUME');
                            $this->playMusic($hg, $mid, $vol);
                        }
                        else
                        {
                            $this->stopMusic($hg);
                        }
                        break;
                }
            }
            else
            {
                foreach ($this->_devices as $device)
                {
                    if ($event['PEERID'] == $device->getPeerId())
                    {
                        if (FALSE !== ($json = $device->updateEvent($hg, $event)))
                        {
                            $json->data->key = $this->getGatewayKey($hg);
                            $cmd = json_encode($json);
                            $this->sendCommand($this->_socket, $cmd, $this->_ip, $this->_port, MiConstants::ACK_WRITE);
                        }
                    }
                }
            }
        }
    }
    
    public function updateData($hg, $response)
    {
        try
        {
            $data = json_decode($response->data);
            if ($this->setProperty($data, 'rgb'))
            {
                $rgb = $data->rgb & 0xffffff;
                $brightness = $data->rgb >> 24;
                if (0 < $brightness)
                {
                    if ($this->_rgb != $data->rgb)
                    {
                        $this->_rgb = $data->rgb;
                        $hg->setValue($this->_peerId, 1, 'RGB_OLD', $data->rgb);
                        $hg->setValue($this->_peerId, 1, 'RGB', $rgb);
                        $hg->setValue($this->_peerId, 1, 'BRIGHTNESS', $brightness);
                    }
                    if (!$this->_enable)
                    {
                        $hg->setValue($this->_peerId, 1, 'ENABLE', TRUE);
                    }
                }
                else
                {
                    if ($this->_enable)
                    {
                        // do not change last rgb value if light is turned off
                        $hg->setValue($this->_peerId, 1, 'ENABLE', FALSE);
                    }
                }
            }
            if ($this->setProperty($data, 'illumination'))
            {
                $hg->setValue($this->_peerId, 1, 'ILLUMINATION', $data->illumination); 
            }
            $this->setProperty($response, 'token');
            // finally update heartbeat timestamp
            $hg->setValue($this->_peerId, 0, 'HEARTBEAT', time()); 
        }
        catch (\Homegear\HomegearException $e)
        {
            MiLogger::Instance()->exception_log($e);
        }
        catch (Exception $e)
        {
            MiLogger::Instance()->exception_log($e);
        }
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
        }
        
        return $result;
    }
    
    public function sendCommand($socket, $cmd, $ip, $port, $ack)
    {
        $result = FALSE;
        try
        {
            MiLogger::Instance()->debug_log($cmd);
            socket_sendto($socket, $cmd, strlen($cmd), 0, $ip, $port);
            $json = null;
            socket_recvfrom($socket, $json, 1024, MSG_WAITALL, $clientIP, $clientPort);
            if (!is_null($json))
            {
                MiLogger::Instance()->debug_log($json);
                $response = json_decode($json); 
                if ($response->cmd === $ack)
                {
                    $result = json_decode($json);
                }
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
    
    public function updateDevice($hg, $sid, $data)
    {
        $success = FALSE;
        try
        {
            if (array_key_exists($sid, $this->_devices))
            {
                $this->_devices[$sid]->updateData($hg, $data);
                $success = TRUE;
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
        return $success;
    }

    public function calcRGBH($brightness, $rgb)
    {
        return intval(hexdec(sprintf("0x%02x%06x", $brightness, $rgb)));
    }

    public function setRGB($hg, $rgbh)
    {
        $cmd = '{"cmd":"write","model":"'.$this->_model.'","sid":"'.$this->_sid.'","short_id":0,"data":"{\"rgb\":'.$rgbh.',"key":"'.$this->getGatewayKey($hg).'"}"}';
        $this->sendCommand($this->_socket, $cmd, $this->_ip, $this->_port, MiConstants::ACK_WRITE);
    }

    public function playMusic($hg, $mid, $vol)
    {
        $cmd = '{"cmd":"write","model":"'.$this->_model.'","sid":"'.$this->_sid.'","short_id":0,"data":"{\"mid\":'.$mid.',\"vol\":'.$vol.',"key":"'.$this->getGatewayKey($hg).'"}"}';
        $this->sendCommand($this->_socket, $cmd, $this->_ip, $this->_port, MiConstants::ACK_WRITE);
    }

    public function stopMusic($hg)
    {
        $cmd = '{"cmd":"write","model":"'.$this->_model.'","sid":"'.$this->_sid.'","short_id":0,"data":"{\"mid\":10000,"key":"'.$this->getGatewayKey($hg).'"}"}';
        $this->sendCommand($this->_socket, $cmd, $this->_ip, $this->_port, MiConstants::ACK_WRITE);
    }

}

