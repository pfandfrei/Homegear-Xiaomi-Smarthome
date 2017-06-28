<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */

abstract class MiBaseClass
{      
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

