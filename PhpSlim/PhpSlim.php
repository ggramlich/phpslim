<?php
require_once 'PhpSlim/AutoLoader.php';

class PhpSlim
{
    const EXCEPTION_TAG = "__EXCEPTION__:";
    const EXCEPTION_STOP_TEST_TAG = "__EXCEPTION__:ABORT_SLIM_TEST:";

    public static function main($arguments)
    {
        if (count($arguments) < 3) {
            die(self::getHelp());
        }
        mb_internal_encoding("UTF-8");
        $port = array_pop($arguments);
        $path = array_pop($arguments);
        set_include_path($path . PATH_SEPARATOR . get_include_path());

        PhpSlim_AutoLoader::start();

        $slimServer = new PhpSlim_Server();
        $nullLogger = new PhpSlim_Logger_Null();
        $slimServer->setLogger($nullLogger, $nullLogger);
        $slimServer->run($port);
    }

    private static function getHelp()
    {
        return "The runPhpSlim script must be started from FitNesse "
            . "with parameters include_path port.\n";
    }

    public static function tagErrorMessage($message)
    {
        return self::EXCEPTION_TAG . $message;
    }

    public static function tagStopTestMessage($message)
    {
        return self::EXCEPTION_STOP_TEST_TAG . $message;
    }

    public static function errorMessage($message)
    {
        return sprintf("message:<<%s>>", $message);
    }
}
