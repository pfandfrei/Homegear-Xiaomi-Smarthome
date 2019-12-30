<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


$basedir = __DIR__.'/../../';
if (file_exists($basedir.'lib/Homegear/'))
{
    include $basedir.'lib/Homegear/Homegear.php';
    include $basedir.'lib/Homegear/Constants.php';

    define('FILTER_SERIAL', \Homegear\Constants\GetPeerId::Filter_Serial);
}
else
{
    define('FILTER_SERIAL', 1);
}

include_once 'MiConstants.php';
include_once 'MiGateway.php';
include_once 'MiLogger.php';

class MiCentral extends Threaded
{
    const FAMILY_ID = 254;  // miscellaneous device

    private $_sharedData;
    private $_socket;
    private $_oldversion;

    public function __construct($sharedData)
    {
        $this->_sharedData = $sharedData;
        
        $hg = new \Homegear\Homegear();
        $version = $hg->getVersion();
        $this->_oldversion = ($version<'Homegear 0.7');
    }

    public function discover()
    {
        if (FALSE === ($socket_recv = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)))
        {
            die("$errstr ($errno)");
        }

        $hg = new \Homegear\Homegear();

        $socket = socket_create(AF_INET, SOCK_DGRAM, 0);
        socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, true);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 5, 'usec' => '0'));
        socket_sendto($socket, MiConstants::CMD_WHOIS, strlen(MiConstants::CMD_WHOIS), 0, MiConstants::MULTICAST_ADDRESS, MiConstants::SERVER_PORT);
        MiLogger::Instance()->debug_log(MiConstants::CMD_WHOIS);
        do
        {
            $data = null;
            socket_recvfrom($socket, $data, 1024, MSG_WAITALL, $from, $port);
            if (!is_null($data))
            {
                MiLogger::Instance()->debug_log($data);
                $response = json_decode($data);
                if (($response->cmd == MiConstants::ACK_IAM)
                    && ($response->model == MiConstants::MODEL_GATEWAY))
                {
                    $this->_sharedData->gateways[] = new MiGateway($response);
                }
            }
        }
        while (!is_null($data));
        socket_close($socket);

        foreach ($this->_sharedData->gateways as $gateway)
        {
            $gateway->get_id_list($hg);
            $this->createDevices($hg, $gateway);
        }
    }

    public function listDevices()
    {         
        echo "─────────┼───────────────────────────┼───────────────┼──────┼───────────────────────────\r\n"; 
        echo "      ID │ Name                      │ Serial Number │ Type │ Type String               \r\n";            
        echo "─────────┼───────────────────────────┼───────────────┼──────┼───────────────────────────\r\n"; 
        
        $hg = new \Homegear\Homegear();
        foreach ($this->_sharedData->gateways as $gateway)
        {
            foreach ($gateway->getDevicelist() as $sid)
            {
                if ($device = $gateway->getDevice($sid))
                {
                    $id = $device->getPeerId();
                    $config = $hg->getAllConfig($id);
                    $name = $config[0]['NAME'];
                    $serial = $config[0]['ADDRESS'];
                    $typeId = $config[0]['TYPE_ID'];
                    $type = $config[0]['TYPE'];
                    echo sprintf("%8d | %25s |    %10s | %4X | %s\r\n", $id, $name, $serial, $typeId, $type);
                }
            }
        }
    }

    public function createDevices($hg, $gateway)
    {
        // create gateway device
        list($address, $serial) = $gateway->encodeSid($gateway->getSid());
        $peerdIds = $hg->getPeerId(FILTER_SERIAL, $serial);
        if (0 == count($peerdIds))
        {
            $peerId = $hg->createDevice(MiCentral::FAMILY_ID, MiGateway::TYPE_ID, $serial, intval($address), /*protoversion*/0x0107);
            if (!$this->_oldversion)
            {
                $hg->putParamset($peerId, 0, ['SID'=> $gateway->getSid(), 'IP' => $gateway->getIpAddress(), 'PORT' => 9898]);
            }
            $gateway->setPeerId($peerId);
        }
        else
        {
            $gateway->setPeerId($peerdIds[0]);
            $gateway->getParamset($hg, 0);
        }

        foreach ($gateway->getDevicelist() as $sid)
        {
            if ($device = $gateway->getDevice($sid))
            {
                list($address, $serial) = $gateway->encodeSid($sid);
                $peerdIds = $hg->getPeerId(FILTER_SERIAL, $serial);
                if (0 == count($peerdIds))
                {                 
                    $peerId = $hg->createDevice(MiCentral::FAMILY_ID, $device->getTypeId(), $serial, intval($address), /*protoversion*/0x0107);
                    if (!$this->_oldversion)
                    {
                        $hg->putParamset($peerId, 0, ['SID' => $sid]);
                    }
                    $device->setPeerId($peerId);
                }
                else
                {
                    $device->setPeerId($peerdIds[0]);
                }
            }
        }

        $gateway->getDeviceData($hg);
    }

    private function updateDevice($hg, $sid, $data)
    {
        $result = FALSE;
        try
        {
            global $sharedData;
            $result = $this->_sharedData->synchronized(
                function() use($sharedData, $hg, $sid, $data)
                {
                    $result = FALSE;
                    foreach ($sharedData->gateways as $gateway)
                    {
                        if ($gateway->updateDevice($hg, $sid, $data))
                        {
                            $result = TRUE;
                        }
                    }
                    return $result;
                }, $this);
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

    public function createSocket()
    {
        if (FALSE === ($this->_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)))
        {
            die("$errstr ($errno)");
        }
        $res = socket_set_option($this->_socket, IPPROTO_IP, MCAST_JOIN_GROUP, array('group' => MiConstants::MULTICAST_ADDRESS, 'interface' => 0));
        $res = @socket_bind($this->_socket, '0.0.0.0', 9898);
        return ($res===FALSE) ? FALSE : $this->_socket;
    }

    public function run()
    {
        $socket_recv = $this->_socket;

        $hg = new \Homegear\Homegear();

        do
        {
            try
            {
                $json = null;
                socket_recvfrom($socket_recv, $json, 1024, MSG_WAITALL, $from, $port);
                if (!is_null($json))
                {
                    $log_unknown = TRUE;
                    $response = json_decode($json);
                    $data = json_decode($response->data);
                    if (property_exists($data, 'error'))
                    {
                        // todo: error handling
                    }
                           
                    MiLogger::Instance()->debug_log($json);
                    
                    switch ($response->cmd)
                    {
                        case MiConstants::HEARTBEAT:
                        case MiConstants::REPORT:
                        case MiConstants::ACK_READ:
                            if ($response->model == MiConstants::MODEL_GATEWAY)
                            {
                                global $sharedData;
                                $log_unknown = !$this->_sharedData->synchronized(
                                    function() use($sharedData, $hg, $response, $data)
                                    {
                                        foreach ($sharedData->gateways as $gateway)
                                        {
                                            if ($gateway->getSid() == $response->sid)
                                            {
                                                $log_unknown = FALSE;
                                                $gateway->updateData($hg, $response);
                                                return TRUE;
                                            }
                                        }
                                        return FALSE;
                                    }, $this);                                    
                            }
                            else
                            {
                                if (FALSE !== $this->updateDevice($hg, $response->sid, $data))
                                {
                                    $log_unknown = FALSE;
                                }
                            }
                            break;
                        case MiConstants::ACK_WRITE:
                            // todo error handling 
                            $log_unknown = FALSE;
                            break;
                    }
                    if ($log_unknown)
                    {
                        $peerId = 0;
                        global $sharedData;
                        $log_unknown = !$this->_sharedData->synchronized(
                            function() use($sharedData, $hg, $response, $data)
                            {
                                foreach ($sharedData->gateways as $gateway)
                                {
                                    if ($peerId = $gateway->createDevice($hg, $response->sid))
                                    {
                                        break;
                                    }
                                }
                            }, $this);  
                        if ($peerId == 0)
                        {
                            MiLogger::Instance()->unknown_log($json);
                        }
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
        }
        while (TRUE);
    }
}
