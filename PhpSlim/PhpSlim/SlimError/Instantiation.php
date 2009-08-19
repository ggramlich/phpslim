<?php
class PhpSlim_SlimError_Instantiation extends PhpSlim_SlimError_Message
{
    public function __construct($className, $argCount, $additional = "")
    {
        $message = sprintf(
            "COULD_NOT_INVOKE_CONSTRUCTOR %s[%d]",
            $className, $argCount
        );
        if (!empty($additional)) {
            $message .= "\n" . $additional;
        }
        parent::__construct($message);
    }
}
