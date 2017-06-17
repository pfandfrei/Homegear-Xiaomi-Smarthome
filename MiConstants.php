<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2017
 */


class MiConstants
{
    const LOGFILE = '/var/log/homegear/mihome.log';
    
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
    
    const MODEL_GATEWAY     = 'gateway';
    const MODEL_CUBE        = 'cube';
    const MODEL_MAGNET      = 'magnet';
    const MODEL_MOTION      = 'motion';
    const MODEL_SWITCH      = 'switch';
    const MODEL_SENSOR_HT   = 'sensor_ht';
}

