<?php
class PhpSlim_Tests_ListExecutorTest extends PhpSlim_Tests_TestCase
{
    private $_executor;
    private $_statements;

    public function setup()
    {
        TestModule_TestSlim::setStaticValue(null);
        $this->_executor = new PhpSlim_ListExecutor();
        $this->_statements = array();
        $this->addStatement('i1', 'import', 'TestModule');
        $this->addStatement('m1', 'make', 'testSlim', 'TestSlim');
    }

    private function addStatement()
    {
        $this->_statements[] = func_get_args();
    }

    private function getResultFomList($id, $resultList)
    {
        $map = $this->pairsToMap($resultList);
        return $map[$id];
    }

    private function pairsToMap($pairs)
    {
        $map = array();
        foreach ($pairs as $pair) {
            $map[$pair[0]] = $pair[1];
        }
        return $map;
    }

    private function checkResults($expectations)
    {
        $results = $this->execute();
        foreach ($expectations as $id => $expected) {
            $result = $this->getResultFomList($id, $results);
            $this->assertEquals($expected, $result);
        }
    }

    private function execute($statements = null)
    {
        if (is_null($statements)) {
            $statements = $this->_statements;
        }
        return $this->_executor->execute($statements);
    }

    public function testRespondsWithOKToImport()
    {
        $this->checkResults(array('i1' => 'OK'));
    }

    public function testCantExecuteAnInvalidOperation()
    {
        $this->addStatement('inv1', 'invalidOperation');
        $results = $this->execute();
        $result = $this->getResultFomList('inv1', $results);
        $message = 'INVALID_STATEMENT: ["inv1", "invalidOperation"].';
        $this->assertErrorMessage($message, $result);
    }

    public function testCantExecuteAMalformedInstruction()
    {
        $this->addStatement('id', 'call', 'notEnoughArguments');
        $results = $this->execute();
        $result = $this->getResultFomList('id', $results);
        $message = 'MALFORMED_INSTRUCTION ' .
            '["id", "call", "notEnoughArguments"].';
        $this->assertErrorMessage($message, $result);
    }

    public function testCantCallAMethodOnAnInstanceThatDoesNotExist()
    {
        $this->addStatement('id', 'call', 'noSuchInstance', 'noSuchMethod');
        $results = $this->execute();
        $result = $this->getResultFomList('id', $results);
        $message = 'NO_INSTANCE noSuchInstance.';
        $this->assertErrorMessage($message, $result);
    }

    public function testAnEmptySetOfInstructionsGivesAnEmptySetOfResults()
    {
        $results = $this->execute(array());
        $this->assertEquals(array(), $results);
    }

    public function testMakeAnInstanceGivenAFullyQualifiedNameInDotFormat()
    {
        $statement = array("m1", "make", "instance", "testModule.TestSlim");
        $results = $this->execute(array($statement));
        $this->assertEquals(array(array('m1', 'OK')), $results);
    }

    public function testCallASimpleMethodInFitnesseForm()
    {
        $this->addStatement('id', 'call', 'testSlim', 'returnString');
        $this->checkResults(array('m1' => 'OK', 'id' => 'string'));
    }

    public function testLaterImportsTakePrecedenceOverEarlyImports()
    {
        $statement = array(
            "i2", "import", "TestModule.ShouldNotFindTestSlimInHere"
        );
        array_unshift($this->_statements, $statement);
        $this->addStatement('id', 'call', 'testSlim', 'returnString');
        $this->checkResults(array('m1' => 'OK', 'id' => 'string'));
    }

    public function testLaterImportsTakePrecedenceOverEarlyImportsDoLater()
    {
        $firstStatement = array_shift($this->_statements);
        $statement = array(
            "i2", "import", "TestModule.ShouldNotFindTestSlimInHere"
        );
        array_unshift($this->_statements, $statement);
        array_unshift($this->_statements, $firstStatement);
        $this->addStatement('id', 'call', 'testSlim', 'returnString');
        $this->checkResults(array('m1' => 'OK', 'id' => 'blah'));
    }

    public function testPassArgumentsToConstructor()
    {
        $this->addStatement(
            'm2', 'make', 'testSlim2', 'TestSlimWithArguments', '3'
        );
        $this->addStatement('c1', 'call', 'testSlim2', 'arg');
        $this->checkResults(array('m2' => 'OK', 'c1' => '3'));
    }

    public function testCallAFunctionMoreThanOnce()
    {
        $this->addStatement("c1", "call", "testSlim", "add", "x", "y");
        $this->addStatement("c2", "call", "testSlim", "add", "a", "b");
        $this->checkResults(array('c1' => 'xy', 'c2' => 'ab'));
    }

    public function testAssignTheReturnValueToASymbol()
    {
        $this->addStatement(
            "id1", "callAndAssign", "v", "testSlim", "add", "x", "y"
        );
        $this->addStatement("id2", "call", "testSlim", "echoValue", '$v');
        $this->checkResults(array('id1' => 'xy', 'id2' => 'xy'));
    }

    public function testCanReplaceMultipleSymbolsInASingleArgument()
    {
        $this->addStatement(
            "id1", "callAndAssign", "v1", "testSlim", "echoValue", "Bob"
        );
        $this->addStatement(
            "id2", "callAndAssign", "v2", "testSlim", "echoValue", "Martin"
        );
        $this->addStatement(
            "id3", "call", "testSlim", "echoValue", 'name: $v1 $v2'
        );
        $this->checkResults(array('id3' => 'name: Bob Martin'));
    }

    public function testIgnoreDollarIfWhatFollowsIsNotASymbol()
    {
        $this->addStatement("id3", "call", "testSlim", "echoValue", '$v1');
        $this->checkResults(array('id3' => '$v1'));
    }

    public function testPassAndReturnAList()
    {
        $l = array('1', '2');
        $this->addStatement("id", "call", "testSlim", "echoValue", $l);
        $this->checkResults(array('id' => $l));
    }

    public function testPassASymbolInAList()
    {
        $this->addStatement(
            "id1", "callAndAssign", 'v', "testSlim", "echoValue", 'x'
        );
        $this->addStatement(
            "id2", "call", "testSlim", "echoValue", array('$v')
        );
        $this->checkResults(array('id2' => array('x')));
    }

    public function testReturnNull()
    {
        $this->addStatement("id", "call", "testSlim", "getNull");
        $this->checkResults(array('id' => null));
    }

    public function testSurviveExecutingAnError()
    {
        $this->addStatement("id", "call", "testSlim", "triggerError");
        $results = $this->execute();
        $result = $this->getResultFomList('id', $results);
        $this->assertContains(PhpSlim::EXCEPTION_TAG, $result);
    }

    public function testSurviveExecutingAnErrorNoExceptionForLowErrorLevel()
    {
        $this->addStatement("id", "call", "testSlim", "triggerError");
        $oldLevel = error_reporting(E_ALL ^ E_WARNING);
        $results = $this->execute();
        error_reporting($oldLevel);
        $result = $this->getResultFomList('id', $results);
        $this->assertFalse($result);
    }

    public function testStopTestExceptionIsReturned()
    {
        $this->addStatement("id", "call", "testSlim", "raiseStopException");
        $results = $this->execute();
        $result = $this->getResultFomList('id', $results);
        $this->assertContains(PhpSlim::EXCEPTION_STOP_TEST_TAG, $result);
        $this->assertStopTestMessage('test stopped in TestSlim', $result);
    }

    public function testSetStaticValueCanBeReadDirectly()
    {
        $this->addStatement("id", "call", "testSlim", "setStaticValue", 'xyz');
        $this->execute();
        $this->assertEquals('xyz', TestModule_TestSlim::getStaticValue());
    }

    public function testStaticValueIsNull()
    {
        $this->assertNull(TestModule_TestSlim::getStaticValue());
    }

    public function testStopTestExceptionPreventsFurtherExecution()
    {
        $this->addStatement("id1", "call", "testSlim", "raiseStopException");
        $this->addStatement("id2", "call", "testSlim", "setStaticValue", 'xyz');
        $results = $this->execute();
        $this->assertNull(TestModule_TestSlim::getStaticValue());
    }

    public function testThereIsNoResultAfterStopTestException()
    {
        $this->addStatement("id1", "call", "testSlim", "raiseStopException");
        $this->addStatement("id2", "call", "testSlim", "setStaticValue", 'xyz');
        $results = $this->execute();
        $resultKeys = array_keys($this->pairsToMap($results));
        $this->assertEquals(array('i1', 'm1', 'id1'), $resultKeys);
    }
}
