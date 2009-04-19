<?php
class PhpSlim_SlimError_StopTest extends PhpSlim_SlimError_Message
{
    public function __construct($message, $code = 0)
    {
        parent::__construct('STOP_TEST ' . $message, $code);
    }
}

