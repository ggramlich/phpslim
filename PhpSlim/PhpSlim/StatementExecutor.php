<?php
class PhpSlim_StatementExecutor
{
    private $_instances = array();
    private $_modules = array();
    private $_libraries = array();
    private $_symbolRepository;
    private $_stopRequested = false;

    const LIBRARY_PREFIX = 'library';

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
            if ($this->isLibraryName($instanceName)) {
                $this->_libraries[] = $instance;
            }
            $this->_instances[$instanceName] = $instance;
            return 'OK';
        } catch (PhpSlim_SlimError $e) {
            return PhpSlim::tagErrorMessage($e->getMessage());
        } catch (Exception $e) {
            return $this->exceptionToString($e);
        }
    }

    private function isLibraryName($instanceName)
    {
        $length = strlen(self::LIBRARY_PREFIX);
        return (self::LIBRARY_PREFIX === substr($instanceName, 0, $length));
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
            return null;
        }
        return $this->_instances[$instanceName];
    }

    private function raiseExceptionIfInstanceUnknown($instanceName)
    {
        if (empty($this->_instances[$instanceName])) {
            $message = "NO_INSTANCE $instanceName.";
            throw new PhpSlim_SlimError_Message($message);
        }
    }

    public function call($instanceName, $methodName, $args = array())
    {
        $methodName = $this->slimToPhpMethod($methodName);
        try {
            $args = (array) $args;
            $callback = $this->getCallback($instanceName, $methodName, $args);
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

    private function getCallback($instanceName, $methodName, $args)
    {
        $instance = $this->instance($instanceName);
        $callback = array($instance, $methodName);
        if (!is_callable($callback) && !is_null($instance)) {
            $callback = $this->getCallbackFromSystemUnderTest(
                $instance, $methodName
            );
        }
        if (!is_callable($callback)) {
            $callback = $this->getCallbackFromLibrary($methodName);
        }
        if (is_callable($callback)) {
            return $callback;
        }

        $this->raiseExceptionIfInstanceUnknown($instanceName);
        $this->raiseNoMethodException($methodName, count($args), $instance);
    }

    private function raiseNoMethodException($methodName, $argCount, $instance)
    {
        $message = sprintf(
            "NO_METHOD_IN_CLASS %s[%d] %s.",
            $methodName, $argCount, get_class($instance)
        );
        throw new PhpSlim_SlimError_Message($message);
    }

    private function getCallbackFromLibrary($methodName)
    {
        foreach (array_reverse($this->_libraries) as $instance) {
            $callback = array($instance, $methodName);
            if (is_callable($callback)) {
                return $callback;
            }
        }
    }

    private function getCallbackFromSystemUnderTest($instance, $methodName)
    {
        return array(
            $this->getSystemUnderTestFromInstance($instance), $methodName
        );
    }

    private function getSystemUnderTestFromInstance($instance)
    {
        $reflectionClass = new ReflectionClass($instance);
        $filter = ReflectionProperty::IS_PUBLIC;
        foreach ($reflectionClass->getProperties($filter) as $property) {
            if ($this->isSystemUnderTestProperty($property)) {
                return $property->getValue($instance);
            }
        }
    }

    private function isSystemUnderTestProperty(ReflectionProperty $property)
    {
        if ($property->getName() == 'systemUnderTest') {
            return true;
        }
        return false !== strpos($property->getDocComment(), '@SystemUnderTest');
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
