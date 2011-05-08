<?php
class PhpSlim_Tests_SlimHelperLibraryTest extends PhpSlim_Tests_TestCase
{
    const SLIM_HELPER_LIBRARY_INSTANCE_NAME = 'SlimHelperLibrary';
    private $_executor;

    public function setup()
    {
        $this->_executor = new PhpSlim_StatementExecutor();
    }

    public function testStatementExecutorConsumerFixtureHasStatementExecutor()
    {
        $this->_executor->create(
            'somefixture',
            'PhpSlim_SlimHelperLibrary',
            array()
        );
        $fixture = $this->_executor->instance('somefixture');
        $this->assertSame(
            $this->_executor, $fixture->getStatementExecutor()
        );
    }

    public function testSlimHelperLibraryIsStoredInSlimExecutor()
    {
        $helperLibrary = $this->getSlimHelperLibraryFromStatementExecutor();
        $this->assertTrue($helperLibrary instanceof PhpSlim_SlimHelperLibrary);
    }

    public function testSlimHelperLibraryHasStatementExecutor()
    {
        $helperLibrary = $this->getSlimHelperLibraryFromStatementExecutor();
        $this->assertSame(
            $this->_executor, $helperLibrary->getStatementExecutor()
        );
    }

    private function getSlimHelperLibraryFromStatementExecutor()
    {
        return $this->_executor->instance(
            self::SLIM_HELPER_LIBRARY_INSTANCE_NAME
        );
    }
}
