<?php
class PhpSlim_ListExecutor
{
    private $_executor;
    private $_results = array();

    public function __construct()
    {
        $this->_executor = new PhpSlim_StatementExecutor();
    }

    public function execute($instructions)
    {
        foreach ($instructions as $instruction) {
            $this->executeInstruction($instruction);
            if ($this->lastResultIsStopTestException()) {
                break;
            }
        }
        return $this->_results;
    }

    private function lastResultIsStopTestException()
    {
        $lastResult = end($this->_results);
        if (!is_string($lastResult[1])) {
            return false;
        }
        return $this->stringResultContainsStopTestMessage($lastResult[1]);
    }

    private function stringResultContainsStopTestMessage($resultString)
    {
        $stopTestMessage = PhpSlim::tagErrorMessage('message:<<STOP_TEST');
        return false !== strpos($resultString, $stopTestMessage);
    }

    private function executeInstruction($instruction)
    {
        $result = PhpSlim_Statement::execute($instruction, $this->_executor);
        $this->_results[] = $result;
    }
}
