<?php
class PhpSlim_Tests_StatementTest extends PhpSlim_Tests_TestCase
{
    private $_statement;

    public function setup()
    {
        $this->_statement = new PhpSlim_StatementExecutor();
    }

    public function testTranslateSlimClassNamesToPhpClassNames()
    {
        $phpClass = $this->_statement->slimToPhpClass('myPackage.MyClass');
        $this->assertEquals('MyPackage_MyClass', $phpClass);
        $phpClass = $this->_statement->slimToPhpClass('this.that::theOther');
        $this->assertEquals('This_That_TheOther', $phpClass);
    }

    public function testTranslateSlimMethodNamesToPhpMethodNames()
    {
        $phpMethod = $this->_statement->slimToPhpMethod('myMethod');
        $this->assertEquals('myMethod', $phpMethod);
    }
}
