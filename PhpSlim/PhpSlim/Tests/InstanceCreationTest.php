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
        $this->assertInstanceOf('TestModule_TestSlim', $x);
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

    public function testCreateAnInstanceWithArgumentsReplaceSymbol()
    {
        $this->_caller->setSymbol('v', 'bob');
        $response = $this->_caller->create(
            'x', 'TestModule_TestSlimWithArguments', array('$v')
        );
        $this->assertEquals('OK', $response);
        $x = $this->_caller->instance('x');
        $this->assertEquals('bob', $x->arg);
    }

    public function testCantCreateInstanceWithTheWrongNumberOfArguments()
    {
        $result = $this->_caller->create(
            'x', 'TestModule_TestSlim', array('optionalArg', 'noSuchArgument')
        );
        $message = 'COULD_NOT_INVOKE_CONSTRUCTOR TestModule_TestSlim[2]';
        $this->assertErrorMessageOpenEnd($message, $result);
    }

    public function testCantCreateAnInstanceIfThereIsNoClass()
    {
        $result = $this->_caller->create(
            'x', 'TestModule_NoSuchClass', array()
        );
        $message = 'COULD_NOT_INVOKE_CONSTRUCTOR TestModule_NoSuchClass[0]';
        $this->assertErrorMessage($message, $result);
    }

    public function testExceptionMessageThrownInConstructorIsPassedThrough()
    {
        $message = 'message thrown';
        $result = $this->_caller->create(
            'x', 'TestModule_ConstructorThrows', array($message)
        );
        $error = 'COULD_NOT_INVOKE_CONSTRUCTOR TestModule_ConstructorThrows[1]';
        $this->assertErrorMessageOpenEnd($error . "\n" . $message, $result);
    }
    
    public function testCantCreateInstanceWithNoPublicConstructor()
    {
        $result = $this->_caller->create(
            'x', 'TestModule_ClassWithNoPublicConstructor', array()
        );
        $message = 'COULD_NOT_INVOKE_CONSTRUCTOR ' .
            'TestModule_ClassWithNoPublicConstructor[0]';
        $this->assertErrorMessage($message, $result);
    }
}
