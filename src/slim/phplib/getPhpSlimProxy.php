<?php


if (!class_exists('PhpSlim_AutoLoader', false)) {
    require_once 'PhpSlim/AutoLoader.php';
}

class PhpSlim
{
    const EXCEPTION_TAG = "__EXCEPTION__:";
    const EXCEPTION_STOP_TEST_TAG = "__EXCEPTION__:ABORT_SLIM_TEST:";

    public static function main($arguments)
    {
        if (count($arguments) < 3) {
            die(self::getHelp());
        }
        mb_internal_encoding("UTF-8");
        $port = array_pop($arguments);
        $path = array_pop($arguments);
        set_include_path($path . PATH_SEPARATOR . get_include_path());

        PhpSlim_AutoLoader::start();

        $slimServer = new PhpSlim_Server();
        $nullLogger = new PhpSlim_Logger_Null();
        $slimServer->setLogger($nullLogger, $nullLogger);
        $slimServer->run($port);
    }

    private static function getHelp()
    {
        return "The runPhpSlim script must be started from FitNesse "
            . "with parameters include_path port.\n";
    }

    public static function tagErrorMessage($message)
    {
        return self::EXCEPTION_TAG . $message;
    }

    public static function tagStopTestMessage($message)
    {
        return self::EXCEPTION_STOP_TEST_TAG . $message;
    }

    public static function errorMessage($message)
    {
        return sprintf("message:<<%s>>", $message);
    }
}

class PhpSlim_AutoLoader
{
    /**
     * @var PhpSlim_AutoLoader
     */
    private static $_instance;

    /**
     * @var boolean
     */
    private $_registered = false;

    /**
     * @var array
     */
    private $_loadedClasses = array();

    protected function __construct()
    {
    }

    public static function singleton()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new PhpSlim_AutoLoader();
        }
        return self::$_instance;
    }

    /**
     * Register the autoload using the singleton
     *
     * @return void
     */
    public static function start()
    {
        self::singleton()->registerAutoLoad();
        self::cleanupIncludePath();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function registerAutoLoad()
    {
        if ($this->_registered) {
            return;
        }
        $this->ensureIncludePath();
        $success = spl_autoload_register(array($this, 'autoload'));
        if (!$success) {
            throw new Exception('Could not register autoload.');
        }
        $this->_registered = true;
    }

    protected static function cleanupIncludePath()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        $trimmedPaths = array_map(array('self', 'trimPath'), $paths);
        $uniquePaths = array_unique($trimmedPaths);
        set_include_path(implode(PATH_SEPARATOR, $uniquePaths));
    }

    private static function trimPath($path)
    {
        return rtrim($path, '\\/');
    }

    /**
     * Ensure that this file is on the include path
     *
     * Sets the include path automatically, if necessary.
     *
     * @return void
     * @throws Exception
     */
    protected function ensureIncludePath()
    {
        // check, if my own class definition is loadable
        $path = $this->getPath(__CLASS__) . '.php';
        if (false === self::getIncludableFile($path)) {
            $basePath = realpath(dirname(__FILE__) . '/..');
            $newPath = get_include_path() . PATH_SEPARATOR . $basePath;
            set_include_path($newPath);
        }
        $this->ensureMyClassIsOnPath($path);
    }

    protected function ensureMyClassIsOnPath($path)
    {
        if (false === self::getIncludableFile($path)) {
            throw new Exception('Cannot set include path');
        }
    }

    /**
     * @param string $class
     * @return void
     */
    public function autoload($class)
    {
        if ($this->classLoaded($class)) {
            // Do not attempt to reload a class.
            // This is especially if the load failed before,
            // otherwise autoload should not be called.
            return;
        }
        if ($path = $this->getFullPathForClass($class)) {
            include_once $path;
        }
        $this->registerClassLoaded($class);
    }

    /**
     * @param string $class
     * @return string
     */
    private static function getFullPathForClass($class)
    {
        $path = self::getPath($class);
        $file = $path . '.php';
        return self::getIncludableFile($file);
    }

    /**
     * @param string $class
     * @return string
     */
    private static function getPath($class)
    {
        return str_replace('_', '/', $class);
    }

    /**
     * @param string $class
     * @return void
     */
    private function registerClassLoaded($class)
    {
        $this->_loadedClasses[] = $class;
    }

    /**
     * @param string $class
     * @return boolean
     */
    private function classLoaded($class)
    {
        return in_array($class, $this->_loadedClasses);
    }

    /**
     * Find appropriate file within include path
     *
     * Returns an absolute path
     *
     * @param string $file
     * @return string
     */
    private static function getIncludableFile($file)
    {
        if (file_exists($file)) {
            return realpath($file);
        }

        $paths = explode(PATH_SEPARATOR, get_include_path() . PATH_SEPARATOR);

        foreach ($paths as $path) {
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;
            if (file_exists($fullpath)) {
                return $fullpath;
            }
        }
        return false;
    }

}

class PhpSlim_AutoLoaderInJar extends PhpSlim_AutoLoader
{
    private static $_instance;

    public static function singleton()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new PhpSlim_AutoLoaderInJar();
        }
        return self::$_instance;
    }

    /**
     * Register the autoload using the singleton
     *
     * @return void
     */
    public static function start()
    {
        self::singleton()->registerAutoLoad();
        self::cleanupIncludePath();
    }

    protected function ensureMyClassIsOnPath($path)
    {
    }
}

class PhpSlim_Java_Proxy
{
    public function getStatementExecutor()
    {
        $executor = new PhpSlim_StatementExecutor();
        return java_closure(new PhpSlim_Java_StatementExecutor($executor));
    }
}

class PhpSlim_Java_StatementExecutor
{
    private $_executor;

    public function __construct(PhpSlim_StatementExecutor $executor)
    {
        $this->_executor = $executor;
    }

    public function setSymbol($name, $valueArray)
    {
        // Unpacking the value from a single element array,
        // see Java PhpStatementExecutor
        $valueArray = java_cast($valueArray, 'array');
        $value = java_values(reset($valueArray));
        $this->_executor->setSymbol(java_cast($name, 'string'), $value);
    }

    public function getSymbol($name)
    {
        $value = $this->_executor->getSymbol(java_cast($name, 'string'));
        return $this->toJavaValue($value);
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
        $args = $this->castArrayContents($args);
        $result = $this->_executor->call(
            java_cast($instanceName, 'string'),
            java_cast($methodName, 'string'),
            $args
        );
        return $this->toJavaValue($result);
    }

    public function getInstance($instanceName)
    {
        $instanceName = java_cast($instanceName, 'string');
        $instance = $this->_executor->instance($instanceName);
        return $this->toJavaValue($instance);
    }

    public function addPath($moduleName)
    {
        $moduleName = java_cast($moduleName, 'string');
        $result = $this->_executor->addModule($moduleName);
        return $this->toJavaValue($result);
    }

    public function stopHasBeenRequested()
    {
        return $this->_executor->stopHasBeenRequested();
    }

    public function reset()
    {
        $this->_executor->reset();
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

class PhpSlim_SlimError extends Exception
{
}

class PhpSlim_SlimError_Instantiation extends PhpSlim_SlimError_Message
{
    public function __construct($className, $argCount)
    {
        $message = sprintf(
            "COULD_NOT_INVOKE_CONSTRUCTOR %s[%d]",
            $className, $argCount
        );
        parent::__construct($message);
    }
}

class PhpSlim_SlimError_Message extends PhpSlim_SlimError
{
    public function __construct($message, $code = 0)
    {
        $message = PhpSlim::errorMessage($message);
        parent::__construct($message, $code);
    }
}


class PhpSlim_SlimError_StopTest extends Exception
{
}


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

class PhpSlim_SymbolRepository
{
    private $_symbols = array();

    public function setSymbol($name, $value)
    {
        $this->_symbols[$name] = $value;
        // Sort it reverse, so for non-prefix-free symbol combinations 
        // the longest symbol is replaced first
        krsort($this->_symbols);
    }

    public function getSymbol($name)
    {
        if (!$this->isSymbolSet($name)) {
            return null;
        }
        return $this->_symbols[$name];
    }

    private function isSymbolSet($name)
    {
        return array_key_exists($name, $this->_symbols);
    }

    public function replaceSymbols(array $list)
    {
        foreach ($list as $key => $item) {
            if (is_array($item)) {
                $list[$key] = $this->replaceSymbols($item);
            } else {
                $list[$key] = $this->replaceSymbolsInItem($item);
            }
        }
        return $list;
    }

    private function replaceSymbolsInItem($item)
    {
        if (empty($item) || is_object($item)) {
            return $item;
        }
        if ($this->itemIsSymbol($item)) {
            // Single symbol, don't replace within string, can return object
            return $this->getSymbol(mb_substr($item, 1));
        }
        $symbolKeys = array_keys($this->_symbols);
        $search = array_map(array($this, 'prependDollar'), $symbolKeys);
        // I tried array_map for the following, but I got a warning.
        $replaceStrings = array();
        foreach ($this->_symbols as $symbolValue) {
            $replaceStrings[] = PhpSlim_TypeConverter::toString($symbolValue);
        }
        return str_replace($search, $replaceStrings, $item);
    }

    private function itemIsSymbol($item)
    {
        if ('$' != mb_substr($item, 0, 1)) {
            return false;
        }
        return $this->isSymbolSet(mb_substr($item, 1));
    }

    private function prependDollar($key)
    {
        return '$' . $key;
    }
}

class PhpSlim_TypeConverter
{
    public static function toString($object)
    {
        if (is_string($object)) {
            return $object;
        }
        if (is_object($object)) {
            if (method_exists($object, 'toString')) {
                return $object->toString();
            }
            if (method_exists($object, '__toString')) {
                return $object->__toString();
            }
        }
        if (self::isNumericArray($object) || self::isBoolArray($object)) {
            return self::inspectArrayNoQuotes($object);
        }
        if (is_bool($object)) {
            return self::boolToString($object);
        }
        if (is_float($object)) {
            return self::floatToString($object);
        }
        if (is_scalar($object)) {
            return (string) $object;
        }
        if (is_array($object)) {
            return self::inspectArray($object);
        }
        if (is_null($object)) {
            return 'null';
        }
        return print_r($object, true);
    }

    public static function inspectArray($array, $quotes = true)
    {
        if (empty($array)) {
            return '[]';
        }
        $array = array_map(array('self', 'toString'), $array);
        if ($quotes) {
            $format = '["%s"]';
            $glue = '", "';
        } else {
            $format = '[%s]';
            $glue = ', ';
        }
        return sprintf($format, implode($glue, $array));
    }
    
    public static function inspectArrayNoQuotes($array)
    {
        return self::inspectArray($array, false);
    }

    public static function listToArray($list)
    {
        if (is_array($list)) {
            return $list;
        }
        return self::parseList($list);
    }

    public static function parseList($list)
    {
        $list = self::removeBrackets($list);
        if (empty($list)) {
            return array();
        }
        return array_map('trim', explode(',', $list));
    }

    private static function removeBrackets($list)
    {
        self::validateListFormat($list);
        $list = mb_substr($list, 1);
        $list = mb_substr($list, 0, -1);
        return trim($list);
    }

    private static function validateListFormat($list)
    {
        if ('[' != mb_substr($list, 0, 1)) {
            throw new PhpSlim_SlimError_Message('List did not start with [');
        }
        if (']' != mb_substr($list, -1)) {
            throw new PhpSlim_SlimError_Message('List did not end with ]');
        }
    }
    
    private static function isNumericArray($array)
    {
        if (!is_array($array)) {
            return false;
        }
        foreach ($array as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }
        return true;
    }
    
    private static function isBoolArray($array)
    {
        if (!is_array($array)) {
            return false;
        }
        foreach ($array as $value) {
            if (!is_bool($value)) {
                return false;
            }
        }
        return true;
    }

    public static function floatToString($value)
    {
        $sign = ($value < 0)? '-': '';
        $value = abs($value);
        $int = floor($value);
        $fract = 10.0 * ($value - $int);
        $percent = substr((string) $fract, 2);
        $fract = (int)((string)$fract);
        $lotsOfSubsequentZeros = strpos($percent, '00000000000');
        if (false !== $lotsOfSubsequentZeros) {
            $percent = substr($percent, 0, $lotsOfSubsequentZeros);
        }
        return $sign . sprintf('%01d.%01d%s', $int, $fract, $percent);
    }

    public static function boolToString($value)
    {
        return $value ? 'true' : 'false';
    }
    
    public static function toBool($string)
    {
        if (is_numeric($string)) {
            return $string != 0;
        }
        $string = strtolower($string);
        return $string == 'yes' || $string == 'true';
    }

    public static function hashListToPairsList($hashList)
    {
        return array_map(array('self', 'hashToPairs'), $hashList);
    }

    public static function hashToPairs($hash)
    {
        $result = array();
        foreach ($hash as $key => $value) {
            $result[] = array($key, $value);
        }
        return $result;
    }

    public static function objectListToPairsList($objects)
    {
        return array_map(array('self', 'objectToPairs'), $objects);
    }

    public static function objectToPairs($object)
    {
        return self::hashToPairs(get_object_vars($object));
    }
}

define('ENGINE_SCOPE', 100);
define('PHP_VAR_PATH', 'PHP_PATH');
define('PHP_VAR_PROXY', 'phpProxy');

$myPath = java_context()->getAttribute(PHP_VAR_PATH, ENGINE_SCOPE);

set_include_path($myPath . PATH_SEPARATOR . get_include_path());
if (!class_exists('PhpSlim_AutoLoaderInJar', false)) {
    require_once 'PhpSlim/AutoLoaderInJar.php';
}
PhpSlim_AutoLoaderInJar::start();

java_context()->setAttribute(PHP_VAR_PROXY, java_closure(new PhpSlim_Java_Proxy()), ENGINE_SCOPE);
java_context()->call(java_closure());

////