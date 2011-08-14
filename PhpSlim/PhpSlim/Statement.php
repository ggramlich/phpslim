<?php
class PhpSlim_Statement
{
    /**
     * @var string
     */
    private $_statement = '';

    /**
     * @var PhpSlim_StatementExecutor
     */
    private $_executor;

    /**
     * @param string $statement
     * @param PhpSlim_StatementExecutor $executor
     * @return array
     */
    public static function execute($statement,
        PhpSlim_StatementExecutor $executor)
    {
        $slimStatement = new PhpSlim_Statement($statement);
        return $slimStatement->exec($executor);
    }

    /**
     * @param string $statement
     * @return void
     */
    public function __construct($statement)
    {
        $this->_statement = $statement;
    }

    /**
     * @param PhpSlim_StatementExecutor $executor
     * @return array
     */
    public function exec(PhpSlim_StatementExecutor $executor)
    {
        $this->_executor = $executor;
        try {
            switch($this->operation()) {
            case 'make':
                $instanceName = $this->getWord(2);
                $className = $this->getWord(3);
                $result = $this->_executor->create(
                    $instanceName, $className, $this->getArgs(4)
                );
                return $this->getExecResultRow($result);
            case 'import':
                $moduleName = $this->getWord(2);
                $this->_executor->addModule($moduleName);
                return $this->getExecResultRow('OK');
            case 'call':
                return $this->call();
            case 'callAndAssign':
                return $this->callAndAssign();
            default:
                throw new PhpSlim_SlimError_Message(
                    sprintf(
                        'INVALID_STATEMENT: %s.',
                        self::inspectArray($this->_statement)
                    )
                );
            }
        } catch (PhpSlim_SlimError $e) {
            return $this->getErrorRow($e->getMessage());
        } catch (Exception $e) {
            return $this->getErrorRow($e->__toString());
        }
    }

    private function getErrorRow($message)
    {
        return $this->getExecResultRow(PhpSlim::tagErrorMessage($message));
    }

    private function getExecResultRow($result)
    {
        return array($this->id(), $result);
    }

    private function call()
    {
        $callback = array($this->_executor, 'call');
        return $this->invokeAtIndex(2, $callback, array());
    }

    private function callAndAssign()
    {
        $callback = array($this->_executor, 'callAndAssign');
        return $this->invokeAtIndex(3, $callback, array($this->getWord(2)));
    }

    private function invokeAtIndex($index, $callback, $invokeArguments)
    {
        $instanceName = $this->getWord($index);
        $methodName = $this->getWord($index + 1);
        $args = $this->getArgs($index + 2);
        array_push($invokeArguments, $instanceName);
        array_push($invokeArguments, $methodName);
        array_push($invokeArguments, $args);
        $result = call_user_func_array($callback, $invokeArguments);
        return $this->getExecResultRow($result);
    }

    private function id()
    {
        return $this->getWord(0);
    }

    private function operation()
    {
        return $this->getWord(1);
    }

    private function getWord($index)
    {
        $this->checkIndex($index);
        return $this->_statement[$index];
    }

    private function getArgs($index)
    {
        return array_slice($this->_statement, $index);
    }

    private function checkIndex($index)
    {
        if ($index >= count($this->_statement)) {
            $message = sprintf(
                "MALFORMED_INSTRUCTION %s.",
                self::inspectArray($this->_statement)
            );
            throw new PhpSlim_SlimError_Message($message);
        }
    }

    public static function inspectArray($array)
    {
        return PhpSlim_TypeConverter::inspectArray($array);
    }

}
