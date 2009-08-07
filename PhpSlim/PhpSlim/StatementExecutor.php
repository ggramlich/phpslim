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
        $classObject = $this->getClassObject($className);
        try {
            $reflectionConstructor = $classObject->getConstructor();
            $argCount = count($constructorArguments);
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
            throw $e;
        } catch (ReflectionException $e) {
            $this->throwInstantiationError($className, $argCount);
        } catch (Exception $e) {
            throw new PhpSlim_SlimError_Message($e->getMessage());
        }
    }

    private function throwInstantiationError($className, $argCount)
    {
        throw new PhpSlim_SlimError_Instantiation($className, $argCount);
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

    private function getClassObject($className)
    {
        return new ReflectionClass($this->requireClass($className));
    }

    public function requireClass($className)
    {
        $fullyQualifiedNames = $this->getFullyQualifiedClassNames($className);
        foreach ($fullyQualifiedNames as $fullyQualifiedName) {
            // Autoloader takes care of requiring the class
            if (class_exists($fullyQualifiedName)) {
                return $fullyQualifiedName;
            }
        }
        $modules = PhpSlim_TypeConverter::inspectArray($this->_modules);
        $message = sprintf(
            'COULD_NOT_INVOKE_CONSTRUCTOR %s failed to find in %s',
            $className, $modules
        );
        throw new PhpSlim_SlimError_Message($message);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getFullyQualifiedClassNames($className)
    {
        $names = array();
        foreach ($this->_modules as $moduleName) {
            $names[] = $moduleName . '_' . $className;
        }
        $names[] = $className;
        return array_reverse($names);
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
