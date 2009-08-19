<?php
class PhpSlim_StatementExecutor
{
    private $_instances = array();
    private $_modules = array();
    private $_symbolRepository;
    private $_stopRequested = false;

    public function __construct()
    {
        $this->_symbolRepository = new PhpSlim_SymbolRepository();
    }

    public function create($instanceName, $className,
        array $constructorArguments)
    {
        try {
            $instance = $this->constructInstance(
                $className, $this->replaceSymbols($constructorArguments)
            );
            $this->_instances[$instanceName] = $instance;
            return 'OK';
        } catch (PhpSlim_SlimError $e) {
            return PhpSlim::tagErrorMessage($e->getMessage());
        } catch (Exception $e) {
            return $this->exceptionToString($e);
        }
    }

    private function constructInstance($className, array $constructorArguments)
    {
        $argCount = count($constructorArguments);
        $classObject = $this->getClassObject($className, $argCount);
        try {
            $reflectionConstructor = $classObject->getConstructor();
            if (empty($reflectionConstructor)) {
                if (empty($constructorArguments)) {
                    return $classObject->newInstance();
                } else {
                    $this->throwInstantiationError($className, $argCount);
                }
            }
            if ($argCount <
                    $reflectionConstructor->getNumberOfRequiredParameters()
                    || $argCount >
                    $reflectionConstructor->getNumberOfParameters()
                ) {
                $this->throwInstantiationError($className, $argCount);
            }
            return $classObject->newInstanceArgs($constructorArguments);
        } catch (PhpSlim_SlimError_Instantiation $e) {
            $this->throwInstantiationError($className, $argCount, $e);
        } catch (ReflectionException $e) {
            $this->throwInstantiationError($className, $argCount);
        } catch (Exception $e) {
            $this->throwInstantiationError($className, $argCount, $e);
        }
    }

    private function throwInstantiationError($className, $argCount, $e = null)
    {
        $additional = '';
        if (!empty($e)) {
            $additional = $e->getMessage() . "\n" . $e;
        }
        throw new PhpSlim_SlimError_Instantiation(
            $className,
            $argCount,
            $additional
        );
    }

    public function instance($instanceName)
    {
        if (empty($this->_instances[$instanceName])) {
            $message = "NO_INSTANCE $instanceName.";
            throw new PhpSlim_SlimError_Message($message);
        }
        return $this->_instances[$instanceName];
    }

    public function call($instanceName, $methodName, $args = array())
    {
        $methodName = $this->slimToPhpMethod($methodName);
        try {
            $args = (array) $args;
            $instance = $this->instance($instanceName);
            $callback = array($instance, $methodName);
            if (!is_callable($callback)) {
                $message = sprintf(
                    "NO_METHOD_IN_CLASS %s[%d] %s.",
                    $methodName, count($args), get_class($instance)
                );
                throw new PhpSlim_SlimError_Message($message);
            }
            $args = $this->replaceSymbols($args);
            set_error_handler(array($this, 'exceptionErrorHandler'));
            $result = call_user_func_array($callback, $args);
            restore_error_handler();
            return $result;
        } catch (PhpSlim_SlimError $e) {
            return PhpSlim::tagErrorMessage($e->getMessage());
        } catch (Exception $e) {
            return $this->exceptionToString($e);
        }
    }

    public function exceptionErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno & error_reporting()) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
    }

    private function exceptionToString(Exception $e)
    {
        if ($this->isStopTestException($e)) {
            $this->_stopRequested = true;
            $message = $e->getMessage();
            if (!empty($message)) {
                $message = PhpSlim::errorMessage($e->getMessage());
            }
            return PhpSlim::tagStopTestMessage($message);
        } else {
            return PhpSlim::tagErrorMessage($e->__toString());
        }
    }

    private function isStopTestException(Exception $e)
    {
        return (false !== strpos(get_class($e), 'StopTest'));
    }

    public function addModule($moduleName)
    {
        $this->_modules[] = str_replace('.', '_', $moduleName);
        return 'OK';
    }

    private function getClassObject($className, $argCount)
    {
        return new ReflectionClass($this->requireClass($className, $argCount));
    }

    public function requireClass($className, $argCount = 0)
    {
        $fullyQualifiedNames = $this->getFullyQualifiedClassNames($className);
        foreach ($fullyQualifiedNames as $fullyQualifiedName) {
            // Autoloader takes care of requiring the class
            if (class_exists($fullyQualifiedName)) {
                return $fullyQualifiedName;
            }
        }
        $this->throwInstantiationError($className, $argCount);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getFullyQualifiedClassNames($className)
    {
        $names = array();
        foreach ($this->_modules as $moduleName) {
            $names[] = $this->slimToPhpClass($moduleName . '.' . $className);
        }
        $names[] = $this->slimToPhpClass($className);
        return array_reverse($names);
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

    public function setSymbol($name, $value)
    {
        $this->_symbolRepository->setSymbol($name, $value);
    }

    public function getSymbol($name)
    {
        return $this->_symbolRepository->getSymbol($name);
    }

    private function replaceSymbols(array $list)
    {
        return $this->_symbolRepository->replaceSymbols($list);
    }

    public function stopHasBeenRequested()
    {
        return $this->_stopRequested;
    }

    public function reset()
    {
        $this->_stopRequested = false;
    }

}
