<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */

abstract class MiBaseClass
{
    const MULTICAST_ADDRESS = '224.0.0.50';
    const MULTICAST_PORT    = 9898;
    const SERVER_PORT       = 4321;
    const SOCKET_BUFSIZE    = 1024;
    
    const CMD_WHOIS         = '{"cmd":"whois"}';
    const CMD_GET_ID_LIST   = '{"cmd":"get_id_list"}';
    const READ_CMD          = '{"cmd":"read"}';
    const CMD_WRITE         = '{"cmd":"write"}';

    const ACK_IAM           = 'iam';
    const ACK_GET_ID_LIST   = 'get_id_list_ack';
    const ACK_READ          = 'read_ack';
    const ACK_WRITE         = 'write_ack';
    
    const HEARTBEAT         = 'heartbeat';
    const REPORT            = 'report';
    
    const MODEL_GATEWAY     = 'gateway';    // 0x286c
    const MODEL_MAGNET      = 'magnet';     // 0x287a
    const MODEL_MOTION      = 'motion';     // 0x287b
    const MODEL_SWITCH      = 'switch';     // 0x287c
    const MODEL_SENSOR_HT   = 'sensor_ht';  // 0x288c
    const MODEL_CUBE        = 'cube';       // 0x28bc
    
    protected $_model = '';
    protected $_sid = '';

    protected function sendCommand($socket, $cmd, $ip, $port, $ack)
    {
        $result = FALSE;
        echo $cmd."\r\n";
        socket_sendto($socket, $cmd, strlen($cmd), 0, $ip, $port);
        $json = null;
        socket_recvfrom($socket, $json, 1024, MSG_WAITALL, $clientIP, $clientPort);
        if (!is_null($json))
        {
            echo $json."\r\n\r\n";
            $response = json_decode($json); 
            if ($response->cmd === $ack)
            {
                $result = json_decode($json);
            }
        }
        
        return $result;
    }
    
    protected function setProperty(&$param, $mixed, $property)
    {
        $result = FALSE;
        if (property_exists($mixed, $property))
        {
            $param = $mixed->$property;
            $result = TRUE;
        }
        
        return $result;
    }
}

