<?php
class PhpSlim_SlimError_Instantiation extends PhpSlim_SlimError_Message
{
    public function __construct($className, $argCount)
    {
        $message = sprintf(
            "COULD_NOT_INVOKE_CONSTRUCTOR %s[%d]",
            $className, $argCount
        );
        parent::__construct($message);
    }
}
