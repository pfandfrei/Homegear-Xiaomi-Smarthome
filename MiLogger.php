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
    
    public function error_log($text, $model='', $type='')
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        if ((strlen($model) > 0) || (strlen($type) > 0))
        {
            error_log($now . ' [ERROR] ' . $text . '(' . $model . ' ' . $type . ')' . PHP_EOL, 3, MiConstants::ERRFILE);
        }
        else
        {
            error_log($now . ' [ERROR] ' . $text . PHP_EOL, 3, MiConstants::ERRFILE);
        }
    }
    
    public function unknown_log($text)
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        error_log($now . ' [UNKNOWN] ' . $text . PHP_EOL, 3, MiConstants::ERRFILE);
    }
    
    public function exception_log($e, $model='', $type='')
    {
        $now = strftime('%Y-%m-%d %H:%M:%S');
        if ((strlen($model) > 0) || (strlen($type) > 0))
        {
            error_log($now . ' [EXCEPTION] '.$e->getFile().' line '.$e->getLine().'('.$e->getCode()." ".$e->getMessage()
                . '|' .$model . ' ' . $type. ')' . PHP_EOL, 3, MiConstants::ERRFILE);
        }
        else
        {
            error_log($now . ' [EXCEPTION] '.$e->getFile().' line '.$e->getLine().'('.$e->getCode()." ".$e->getMessage().')' . PHP_EOL, 3, MiConstants::ERRFILE);
        }
        error_log($e->getTraceAsString());
    }
}

