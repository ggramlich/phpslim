<?php
class PhpSlim_Tests_SlimHelperLibraryTest extends PhpSlim_Tests_TestCase
{
    const SLIM_HELPER_LIBRARY_INSTANCE_NAME = 'SlimHelperLibrary';
    const ACTOR_INSTANCE_NAME = 'scriptTableActor';
    const TEST_CLASS = 'TestModule_TestSlim';
    
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

    public function testSlimHelperLibraryCanPushAndPopFixture()
    {
        $helperLibrary = $this->getSlimHelperLibraryFromStatementExecutor();
        $this->_executor->create(
            self::ACTOR_INSTANCE_NAME,
            self::TEST_CLASS,
            array()
        );
        $firstActor = $this->_executor->instance(self::ACTOR_INSTANCE_NAME);

        $helperLibrary->pushFixture();
        
        $this->_executor->create(
            self::ACTOR_INSTANCE_NAME,
            self::TEST_CLASS,
            array('1')
        );
        $currentActor = $this->_executor->instance(self::ACTOR_INSTANCE_NAME);
        $this->assertNotSame($firstActor, $currentActor);
        
        $helperLibrary->popFixture();
        
        $currentActor = $this->_executor->instance(self::ACTOR_INSTANCE_NAME);
        $this->assertSame($firstActor, $currentActor);
    }
    
    private function getSlimHelperLibraryFromStatementExecutor()
    {
        return $this->_executor->instance(
            self::SLIM_HELPER_LIBRARY_INSTANCE_NAME
        );
    }
}
