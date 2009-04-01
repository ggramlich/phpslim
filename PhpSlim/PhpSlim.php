<?php
class PhpSlim
{
    const EXCEPTION_TAG = "__EXCEPTION__:";
    
    public static function tagErrorMessage($message)
    {
        return self::EXCEPTION_TAG . $message;
    }
    
}
