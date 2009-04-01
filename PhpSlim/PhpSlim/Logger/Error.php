<?php
class PhpSlim_Logger_Error implements PhpSlim_Logger
{
    public function log($string)
    {
        error_log($string);
    }
}
