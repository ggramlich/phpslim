<?php
class TestModule_SystemUnderTest_SimpleFixture
{
    private $_echoCalled = false;

    public function echoString()
    {
        $this->_echoCalled = true;
    }

    public function echoCalled()
    {
        return $this->_echoCalled;
    }
}
