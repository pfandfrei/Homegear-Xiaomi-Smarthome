<?php
/*
 * Homegear Xiaomi Smarthome V0.1 for homegear 0.7.x
 * (c) Frank Motzkau 2018
 */

include_once 'MiConstants.php';
final class MiLogger
{
    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new MiLogger();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instantiate it
     *
     */
    private function __construct()
    {

    }
    
    public function debug_log($message)
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        error_log($now . ' >>  ' . $message . PHP_EOL, 3, MiConstants::LOGFILE);
    }
    
    public function error_log($text)
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        error_log('ERROR >> ' . $now . ' >>  ' . $text . PHP_EOL, 3, MiConstants::LOGFILE);
    }
    
    public function unknown_log($text)
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        error_log('UNKNOWN >> ' . $now . ' >>  ' . $text . PHP_EOL, 3, MiConstants::LOGFILE);
    }
    
    public function exception_log($e)
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        error_log('ERROR >> ' . $now . ' >>  '.$e->getFile().' line '.$e->getLine().'('.$e->getCode()." ".$e->getMessage().')' . PHP_EOL, 3, MiConstants::LOGFILE);
    }
}

