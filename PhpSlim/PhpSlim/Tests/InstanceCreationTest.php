<?php
class PhpSlim_Tests_InstanceCreationTest extends PhpSlim_Tests_TestCase
{
    private $_caller;

    public function setup()
    {
        $this->_caller = new PhpSlim_StatementExecutor();
    }

    public function testCreateAnInstance()
    {
        $response = $this->_caller->create('x', 'TestModule_TestSlim', array());
        $this->assertEquals('OK', $response);
        $x = $this->_caller->instance('x');
        $this->assertType('TestModule_TestSlim', $x);
    }

    public function testCreateAnInstanceWithArguments()
    {
        $response = $this->_caller->create(
            'x', 'TestModule_TestSlimWithArguments', array('3')
        );
        $this->assertEquals('OK', $response);
        $x = $this->_caller->instance('x');
        $this->assertEquals('3', $x->arg);
    }

    public function testCantCreateInstanceWithTheWrongNumberOfArguments()
    {
        $result = $this->_caller->create(
            'x', 'TestModule_TestSlim', array('noSuchArgument')
        );
        $message = 'COULD_NOT_INVOKE_CONSTRUCTOR TestModule_TestSlim[1]';
        $this->assertErrorMessage($message, $result);
    }

    public function testCantCreateAnInstanceIfThereIsNoClass()
    {
        $result = $this->_caller->create(
            'x', 'TestModule_NoSuchClass', array()
        );
        $message = 'COULD_NOT_INVOKE_CONSTRUCTOR TestModule_NoSuchClass ' .
            'failed to find in []';
        $this->assertErrorMessage($message, $result);
    }
}
