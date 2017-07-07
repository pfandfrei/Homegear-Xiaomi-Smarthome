<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
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

class MiCentral extends Threaded
{
    const FAMILY_ID = 254;  // miscellaneous device

    private $_gateways;
    private $_socket;

    public function __construct()
    {
        $this->_gateways = new StackableArray();
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
        do
        {
            $data = null;
            socket_recvfrom($socket, $data, 1024, MSG_WAITALL, $from, $port);
            if (!is_null($data))
            {
                $response = json_decode($data);
                if (($response->cmd == MiConstants::ACK_IAM)
                    && ($response->model == MiConstants::MODEL_GATEWAY))
                {
                    $this->_gateways[] = new MiGateway($response);
                }
            }
        }
        while (!is_null($data));
        socket_close($socket);

        foreach ($this->_gateways as $gateway)
        {
            $gateway->get_id_list($hg);
            $this->createDevices($hg, $gateway);
        }

        return $this->_gateways;
    }

    private function encodeSid($sid)
    {
        $id = substr('0000000000000000'. $sid, 16);
        $address = intval(base_convert(substr($id, -16, 8), 16, 10));
        $serial = 'MI'.strtoupper(substr($id, -8));
        return [$address, $serial];
    }

    public function createDevices($hg, $gateway)
    {
        // create gateway device
        list($address, $serial) = $this->encodeSid($gateway->getSid());
        $peerdIds = $hg->getPeerId(FILTER_SERIAL, $serial);
        if (0 == count($peerdIds))
        {
            $peerId = $hg->createDevice(MiCentral::FAMILY_ID, MiGateway::TYPE_ID, $serial, intval($address), /*protoversion*/0x0107);
            $hg->putParamset($peerId, 0, ['SID'=> $gateway->getSid(), 'IP' => $gateway->getIpAddress(), 'PORT' => 9898]);
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
                list($address, $serial) = $this->encodeSid($sid);
                $peerdIds = $hg->getPeerId(FILTER_SERIAL, $serial);
                if (0 == count($peerdIds))
                {
                    $peerId = $hg->createDevice(MiCentral::FAMILY_ID, $device->getTypeId(), $serial, intval($address), /*protoversion*/0x0107);
                    $hg->putParamset($peerId, 0, ['SID' => $sid]);
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
        foreach ($this->_gateways as $gateway)
        {
            if ($gateway->updateDevice($hg, $sid, $data))
            {
                $result = $gateway;
            }
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
        $res = socket_bind($this->_socket, '0.0.0.0', 9898) or die("Could not bind");
        return $this->_socket;
    }

    public function run()
    {
        $socket_recv = $this->_socket;

        $hg = new \Homegear\Homegear();

        do
        {
            $json = null;
            socket_recvfrom($socket_recv, $json, 1024, MSG_WAITALL, $from, $port);
            if (!is_null($json))
            {
                $response = json_decode($json);
                $data = json_decode($response->data);
                switch ($response->cmd)
                {
                    case MiConstants::HEARTBEAT:
                    case MiConstants::REPORT:
                    case MiConstants::ACK_READ:
                        if ($response->model == MiConstants::MODEL_GATEWAY)
                        {
                            foreach ($this->_gateways as $gateway)
                            {
                                if ($gateway->getSid() == $response->sid)
                                {
                                    $gateway->debug_log($json);
                                    if (property_exists($data, 'error'))
                                    {
                                        // todo: error handling
                                    }
                                    $gateway->updateData($hg, $response);
                                    break;
                                }
                            }
                        }
                        else
                        {
                            if (FALSE !== ($gateway = $this->updateDevice($hg, $response->sid, $data)))
                            {
                                $gateway->debug_log($json);
                            }
                        }
                        break;
                    case MiConstants::ACK_WRITE:
                        // todo error handling 
                        break;
                }
            }
        }
        while (TRUE);
    }
}
