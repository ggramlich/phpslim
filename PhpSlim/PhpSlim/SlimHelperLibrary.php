<?php
class PhpSlim_SlimHelperLibrary implements PhpSlim_StatementExecutorConsumer
{
    private $_statementExecutor;
    
    public function setStatementExecutor($statementExecutor)
    {
        $this->_statementExecutor = $statementExecutor;
    }
    
    public function getStatementExecutor()
    {
        return $this->_statementExecutor;
    }
}
