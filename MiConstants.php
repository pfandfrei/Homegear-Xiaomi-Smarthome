<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2018
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
    
    const MODEL_UNKNOWN         = '';
    const MODEL_GATEWAY         = 'gateway';                // 0x286c
    const MODEL_VIBRATION       = 'vibration';              // 0x2877
    const MODEL_SWITCH_AQ3      = 'lumi.sensor_switch.aq3'; // 0x2878
    const MODEL_MAGNET_AQ2      = 'sensor_magnet.aq2';      // 0x2879
    const MODEL_MAGNET          = 'magnet';                 // 0x287a
    const MODEL_MOTION          = 'motion';                 // 0x287b
    const MODEL_SWITCH          = 'switch';                 // 0x287c
    const MODEL_SWITCH_AQ2      = 'sensor_switch.aq2';      // 0x287d
    const MODEL_MOTION_AQ2      = 'sensor_motion.aq2';      // 0x287e
    const MODEL_WLEAK_AQ1       = 'sensor_wleak.aq1';       // 0x287f
    
    const MODEL_SENSOR_HT       = 'sensor_ht';              // 0x288c
    const MODEL_WEATHER_V1      = 'weather.v1';             // 0x288d
    
    const MODEL_CUBE            = 'cube';                   // 0x28bc
    const MODEL_CUBE_AQGL01     = 'sensor_cube.aqgl01';     // 0x28bc
    
    const MODEL_CTRL_NEUTRAL1   = 'ctrl_neutral1';          // 0x28c0
    const MODEL_86SW1           = '86sw1';                  // 0x28c1
    const MODEL_CTRL_LN1        = 'ctrl_ln1';               // 0x28c2
    
    const MODEL_CTRL_NEUTRAL2   = 'ctrl_neutral2';          // 0x28d0
    const MODEL_86SW2           = '86sw2';                  // 0x28d1
    const MODEL_CTRL_LN2        = 'ctrl_ln2';               // 0x28d2
    
    const MODEL_SMOKE           = 'smoke';                  // 0x28e0
    const MODEL_NATGAS          = 'natgas';                 // 0x28e1
    
    const MODEL_PLUG            = 'plug';                   // 0x28f0
    const MODEL_86PLUG          = '86plug';                 // 0x28f1
}

