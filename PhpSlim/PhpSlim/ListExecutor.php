<?php
class PhpSlim_ListExecutor
{
    private $_executor;

    public function __construct()
    {
        $this->_executor = new PhpSlim_StatementExecutor();
    }
    
    public function execute($instructions)
    {
        return array_map(array($this, 'executeInstruction'), $instructions);
    }
    
    private function executeInstruction($instruction)
    {
        return PhpSlim_Statement::execute($instruction, $this->_executor);
    }
}
