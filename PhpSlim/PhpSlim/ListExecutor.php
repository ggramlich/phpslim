<?php
class PhpSlim_ListExecutor
{
    private $_executor;
    private $_results;

    public function __construct()
    {
        $this->_executor = new PhpSlim_StatementExecutor();
    }

    public function execute($instructions)
    {
        $this->_results = array();
        foreach ($instructions as $instruction) {
            $this->_results[] = $this->executeInstruction($instruction);
            if ($this->_executor->stopHasBeenRequested()) {
                $this->_executor->reset();
                return $this->_results;
            }
        }
        return $this->_results;
    }

    private function executeInstruction($instruction)
    {
        return PhpSlim_Statement::execute($instruction, $this->_executor);
    }
}
