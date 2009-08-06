<?php
class PhpSlim_Java_StatementExecutor
{
    private $_executor;

    public function __construct(PhpSlim_StatementExecutor $executor)
    {
        $this->_executor = $executor;
    }

    public function setSymbol($name, $value)
    {
        $this->_executor->setSymbol(java_values($name), java_values($value));
    }

    public function getSymbol($name)
    {
        return $this->_executor->getSymbol(java_values($name));
    }

    public function create($instanceName, $className,
        $constructorArguments)
    {
        $constructorArguments = java_cast($constructorArguments, 'array');
        $constructorArguments = $this->castArrayContents($constructorArguments);
        return $this->_executor->create(
            java_cast($instanceName, 'string'),
            java_cast($className, 'string'),
            $constructorArguments
        );
    }

    public function call($instanceName, $methodName, $args)
    {
        $args = java_cast($args, 'array');
        $result = $this->_executor->call(
            java_cast($instanceName, 'string'),
            java_cast($methodName, 'string'),
            $this->castArrayContents($args)
        );
        return $this->toJavaValue($result);
    }

    public function instance($instanceName)
    {
        $instanceName = java_cast($instanceName, 'string');
        $instance = $this->_executor->instance($instanceName);
        return $this->toJavaValue($instance);
    }

    private function castArrayContents(array $args)
    {
        $casted = array();
        foreach ($args as $key => $arg) {
            $casted[$key] = java_values($arg);
        }
        return $casted;
    }

    private function toJavaValue($value)
    {
        if (is_null($value)) {
            return null;
        }
        if (is_object($value)) {
            return java_closure($value);
        }
        if (is_array($value)) {
            return $this->toJavaList($value);
        }
        return $this->toString($value);
    }

    private function toString($value)
    {
        return PhpSlim_TypeConverter::toString($value);
    }

    private function toJavaList(array $values)
    {
        $javaList = new java("java.util.ArrayList");
        foreach ($values as $value) {
            $javaList->add($this->toJavaValue($value));
        }
        return $javaList;
    }
    
}
