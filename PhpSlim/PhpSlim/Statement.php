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
                $className = $this->slimToPhpClass($this->getWord(3));
                $result = $this->_executor->create(
                    $instanceName, $className, $this->getArgs(4)
                );
                return $this->getExecResultRow($result);
            case 'import':
                $moduleName = $this->slimToPhpClass($this->getWord(2));
                $this->_executor->addModule($moduleName);
                return $this->getExecResultRow('OK');
            case 'call':
                return $this->callMethodAtIndex(2);
            case 'callAndAssign':
                $result = $this->callMethodAtIndex(3);
                $this->_executor->setSymbol($this->getWord(2), $result[1]);
                return $result;
            default:
                throw new PhpSlim_SlimError_Message(sprintf(
                    'INVALID_STATEMENT: %s.',
                    self::inspectArray($this->_statement)
                ));
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

    private function callMethodAtIndex($index)
    {
        $instanceName = $this->getWord($index);
        $methodName = $this->slimToPhpMethod($this->getWord($index + 1));
        $args = $this->getArgs($index + 2);
        $result = $this->_executor->call($instanceName, $methodName, $args);
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

    /**
     * @param string $className
     * @return string
     */
    public function slimToPhpClass($className)
    {
        $parts = preg_split('/\.|\:\:|\_/', $className);
        $converted = array_map('ucfirst', $parts);
        return implode('_', $converted);
    }

    /**
     * @param string $method
     * @return string
     */
    public function slimToPhpMethod($method)
    {
        return strtolower(mb_substr($method, 0, 1)) . mb_substr($method, 1);
    }

    public static function inspectArray($array)
    {
        return PhpSlim_TypeConverter::inspectArray($array);
    }

}
