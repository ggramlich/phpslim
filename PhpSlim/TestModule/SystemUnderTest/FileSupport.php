<?php
class TestModule_SystemUnderTest_FileSupport
{
    private $_deleteCalled = false;

    public function delete()
    {
        $this->_deleteCalled = true;
    }

    public function deleteCalled()
    {
        return $this->_deleteCalled;
    }
}
