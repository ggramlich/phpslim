<?php
class PhpSlim
{
    const EXCEPTION_TAG = "__EXCEPTION__:";
    const EXCEPTION_STOP_TEST_TAG = "__EXCEPTION__:ABORT_SLIM_TEST:";

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
