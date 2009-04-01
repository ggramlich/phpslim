<?php
class TestModule_SimpleScript
{
    private $_arg;
    
    public function setArg($arg)
    {
        $this->_arg = $arg;
    }

    public function getArg()
    {
        return $this->_arg;
    }
}
