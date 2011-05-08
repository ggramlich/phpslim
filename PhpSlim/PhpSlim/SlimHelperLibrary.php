<?php
class PhpSlim_SlimHelperLibrary implements PhpSlim_StatementExecutorConsumer
{
    const ACTOR_INSTANCE_NAME = 'scriptTableActor';

    private $_statementExecutor;
    private $_fixtureStack = array();

    public function setStatementExecutor($statementExecutor)
    {
        $this->_statementExecutor = $statementExecutor;
    }
    
    public function getStatementExecutor()
    {
        return $this->_statementExecutor;
    }
    
    public function pushFixture()
    {
        array_push($this->_fixtureStack, $this->getFixture());
    }

    public function popFixture()
    {
        $fixture = array_pop($this->_fixtureStack);
        $this->_statementExecutor->setInstance(
            self::ACTOR_INSTANCE_NAME, $fixture
        );
    }
    
    public function getFixture()
    {
        return $this->_statementExecutor->instance(self::ACTOR_INSTANCE_NAME);
    }
}
