<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2017
 */


$basedir = __DIR__.'/../../';
if (file_exists($basedir.'lib/Homegear/'))
{
    include $basedir.'lib/Homegear/Homegear.php';
}

include_once 'MiConstants.php';
include_once 'MiCentral.php';
class SharedData extends Threaded
{
    private $scriptId = 0;
    private $peerId = 0;
    public $gateways;
    public $socket_recv;

    public function __construct($scriptId, $peerId = 0)
    {
        $this->scriptId = $scriptId;
        $this->peerId = $peerId;
    }
}

class EventThread extends Thread
{

    const LOGFILE = '/var/log/homegear/mihome.log';
    
    private $sharedData;

    public function __construct($sharedData)
    {
        $this->sharedData = $sharedData;
    }

    public function run()
    {
        $hg = new \Homegear\Homegear();
        if ($hg->registerThread($this->sharedData->scriptId) === false)
        {
            $hg->log('Could not register thread.');
            return;
        }
        
        foreach ($this->sharedData->gateways as $gateway)
        {
            $hg->subscribePeer(intval($gateway->getPeerId()));
            foreach ($gateway->getDevicelist() as $sid)
            {
                if ($device = $gateway->getDevice($sid))
                {
                    $hg->subscribePeer($device->getPeerId());
                }
            }
        }
        
        while (!$hg->shuttingDown())
        {
            $result = $hg->pollEvent();
            if ($result['TYPE'] == 'event')
            {
                for ($i = 0; $i < count($this->sharedData->gateways); $i++)
                {
                    // Pass result to main thread
                    $this->sharedData->gateways[$i]->updateEvent($hg, $result);
                }

                // Wake up main thread
                $this->synchronized(function($thread)
                {
                    $thread->notify();
                }, $this);
            }
            else if ($result['TYPE'] == 'updateDevice')
            {
                for ($i = 0; $i < count($this->sharedData->gateways); $i++)
                {
                    // Pass result to main thread
                    $this->sharedData->gateways[$i]->getParamset($hg, $result['CHANNEL']);
                }                
            }
        }
    }

    public function log($message)
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        error_log($now . ' >>  ' . $message . PHP_EOL, 3, MiConstants::LOGFILE);
    }

}

$hg = new \Homegear\Homegear();
$peerId = (integer) $argv[0];
$scriptId = $hg->getScriptId();
if (!$peerId)
{
    echo "#### MiSmartHome for Homegear 0.7.x ####\r\n";
    echo "#### auto-discovering devices ...   ####\r\n";
}

$central = new MiCentral();
$sharedData = new SharedData($scriptId, $peerId);
    
if ($peerId > 0)
{    
    // create a socket to communicate with gateway
    $sharedData->socket_recv = $central->createSocket();  
    if (FALSE !== $sharedData->socket_recv)
    {
        // discover all gateways and devices
        $sharedData->gateways = $central->discover();
        // handle homegear events
        $thread = new EventThread($sharedData);
        $thread->start();
        // handle gateway communication
        $central->run();
        $thread->join();
        socket_close($sharedData->socket_recv);
    }
}
else
{
    $hg = new \Homegear\Homegear();
    $version = $hg->getVersion();
    if ($version<'Homegear 0.7')
    {
        echo "\r\n\r\n\033[31m#### warning: $version is not supported\r\n";
        echo "### maybe it will work, but it is untested\r\n";
        echo "### please update to Homegear 0.7.x or newer\033[0m\r\n";
    }
    $central->discover();
    echo "\r\n\r\nfound the following devices:\r\n";
    $central->listDevices();
    echo "\r\nInstallation completed!\r\n\r\n";
}
