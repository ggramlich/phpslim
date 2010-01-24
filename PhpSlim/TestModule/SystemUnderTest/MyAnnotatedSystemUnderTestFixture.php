<?php
class TestModule_SystemUnderTest_MyAnnotatedSystemUnderTestFixture
{
    private $_echoCalled = false;

    /**
     * @SystemUnderTest
     */
    public $mySut;

    public function __construct()
    {
        $this->mySut = new TestModule_SystemUnderTest_MySystemUnderTest();
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
        return $this->mySut;
    }
}
