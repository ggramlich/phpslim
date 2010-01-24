<?php
class TestModule_SystemUnderTest_FixtureWithNamedSystemUnderTest
{
    private $_echoCalled = false;

    public $systemUnderTest;

    public function __construct()
    {
        $this->systemUnderTest =
            new TestModule_SystemUnderTest_MySystemUnderTest();
    }

    public function echoString()
    {
        $this->_echoCalled = true;
    }

    public function echoCalled()
    {
        return $this->_echoCalled;
    }

    public function getSystemUnderTest()
    {
        return $this->systemUnderTest;
    }
}

