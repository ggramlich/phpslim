<?php
class PhpSlim_Logger_Echo implements PhpSlim_Logger
{
    public function log($string)
    {
        echo $string . "\n";
    }
}
