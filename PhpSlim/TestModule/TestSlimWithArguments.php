<?php
class TestModule_TestSlimWithArguments
{
    public $arg;

    public function __construct($arg)
    {
        $this->arg = $arg;
    }
    
    public function arg()
    {
        return $this->arg;
    }
}
