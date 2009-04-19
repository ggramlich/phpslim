<?php
class PhpSlim_Tests_MethodInvocationTest extends PhpSlim_Tests_TestCase
{
    private $_executor;
    private $_testSlim;

    public function setup()
    {
        $executor = new PhpSlim_StatementExecutor();
        $result = $executor->create('testSlim', 'TestModule_TestSlim', array());
        $this->assertEquals('OK', $result);
        $this->_testSlim = $executor->instance('testSlim');
        $this->_executor = $executor;
    }

    public function testCallAMethodWithNoArguments()
    {
        $this->_testSlim->expect('noArgs', array());
        $this->_executor->call('testSlim', 'noArgs');
        $this->assertTrue($this->_testSlim->goodCall);
    }

    public function testCantCallAMethodThatDoesNotExist()
    {
        $result = $this->_executor->call('testSlim', 'noSuchMethod');
        $message = 'NO_METHOD_IN_CLASS noSuchMethod[0] TestModule_TestSlim.';
        $this->assertErrorMessage($message, $result);
    }

    public function testCallAMethodThatReturnsAValue()
    {
        $this->_testSlim->expect('returnValue', array(), 'args');
        $result = $this->_executor->call('testSlim', 'returnValue');
        $this->assertTrue($this->_testSlim->goodCall);
        $this->assertEquals('args', $result);
    }

    public function testCallAMethodThatTakesAnArgument()
    {
        $this->_testSlim->expect('oneArg', array('arg'));
        $this->_executor->call('testSlim', 'oneArg', 'arg');
        $this->assertTrue($this->_testSlim->goodCall);
    }

    public function testCallAMethodThatTakesAnArgumentGivenAsArray()
    {
        $this->_testSlim->expect('oneArg', array('arg'));
        $this->_executor->call('testSlim', 'oneArg', array('arg'));
        $this->assertTrue($this->_testSlim->goodCall);
    }

    public function testCallAMethodThatTakesAnArgumentArray()
    {
        $this->_testSlim->expect('oneArg', array(array('a', 'b')));
        $this->_executor->call('testSlim', 'oneArg', array(array('a', 'b')));
        $this->assertTrue($this->_testSlim->goodCall);
    }

    public function testCantCallAMethodOnAnInstanceThatDoesNotExist()
    {
        $result = $this->_executor->call('noSuchInstance', 'noSuchMethod');
        $message = 'NO_INSTANCE noSuchInstance';
        $this->assertErrorMessage($message, $result);
    }

    public function testCallMethodThatRaisesStopException()
    {
        $result = $this->_executor->call('testSlim', 'raiseStopException');
        $message = 'STOP_TEST test stopped in TestSlim';
        $this->assertErrorMessage($message, $result);
    }

    public function testCallEchoValueWithArray()
    {
        $result = $this->_executor->call(
            'testSlim', 'echoValue', array(array('a', 'b'))
        );
        $this->assertEquals(array('a', 'b'), $result);
    }

    public function testReplaceSymbolExpressionsWithTheirValues()
    {
        $this->_executor->setSymbol('v', 'bob');
        $result = $this->_executor->call('testSlim', 'echoValue', 'hi $v.');
        $this->assertEquals('hi bob.', $result);
    }

    public function testReplaceSymbolExpressionsWithTheirValuesArray()
    {
        $this->_executor->setSymbol('v', array('a', 'b'));
        $result = $this->_executor->call('testSlim', 'echoValue', '$v');
        $this->assertEquals(array('a', 'b'), $result);
    }

}

