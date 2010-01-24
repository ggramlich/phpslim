<?php
class TestModule_SystemUnderTest_MySystemUnderTest
{
    private $_echoCalled = false;
    private $_speakCalled = false;

    public function echoString()
    {
        $this->_echoCalled = true;
    }

    public function echoCalled()
    {
        return $this->_echoCalled;
    }

    public function speak()
    {
        $this->_speakCalled = true;
    }

    public function speakCalled()
    {
        return $this->_speakCalled;
    }
}
