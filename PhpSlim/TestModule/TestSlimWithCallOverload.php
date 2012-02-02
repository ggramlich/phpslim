<?php
class TestModule_TestSlimWithCallOverload
{
    public $unknownMethodName;
    public $arguments;

    public function __call($methodName, $arguments)
    {
        $this->unknownMethodName = $methodName;
        $this->arguments = $arguments;
    }
}
