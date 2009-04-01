<?php
class PhpSlim_SlimError_Message extends PhpSlim_SlimError
{
    public function __construct($message, $code = 0)
    {
        $message = sprintf("message:<<%s>>", $message);
        parent::__construct($message, $code);
    }
}

