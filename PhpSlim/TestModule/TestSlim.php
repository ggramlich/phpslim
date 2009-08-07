<?php
class TestModule_TestSlim
{
    public $goodCall = false;

    public $niladWasCalled = false;

    public $value;
    public $list;
    public $stringArray;
    public $doubleArray;

    public $intArg;
    public $doubleArg;
    public $charArg;

    private $_expectedMethod;
    private $_expectedArgs;
    private $_expectedReturn;

    private $_integerArray;
    private $_booleanArray;

    private static $_staticValue;

    private $_constructorArg;

    public function __construct($constructorArg = 0)
    {
        $this->_constructorArg = $constructorArg;
    }

    public function returnConstructorArg()
    {
        return $this->_constructorArg;
    }

    public function echoValue($x)
    {
        return $x;
    }

    public function returnString()
    {
        return 'string';
    }

    public function returnInt()
    {
        return 7;
    }

    public function add($a, $b)
    {
        return $a . $b;
    }

    public function addTo($a, $b)
    {
        return $a + $b;
    }

    public function setIntegerArray($array)
    {
        if (!is_array($array)) {
            $array = self::convertListToIntegerArray($array);
        }
        $this->_integerArray = $array;
    }

    public function getIntegerArray()
    {
        return $this->_integerArray;
    }

    public function getIntegerArrayAsString()
    {
        return PhpSlim_TypeConverter::toString($this->_integerArray);
    }

    public function setBooleanArray($array)
    {
        $array = PhpSlim_TypeConverter::listToArray($array);
        $callback = array('PhpSlim_TypeConverter', toBool);
        $this->_booleanArray = array_map($callback, $array);
    }

    public function getBooleanArray()
    {
        return PhpSlim_TypeConverter::toString($this->_booleanArray);
    }

    private static function convertListToIntegerArray($list)
    {
        $array = PhpSlim_TypeConverter::listToArray($list);
        try {
            $result = array_map(array('self', 'castToInt'), $array);
        } catch (Exception $e) {
            throw new Exception('message:<<CANT_CONVERT_TO_INTEGER_LIST>>');
        }
        return $result;
    }

    public static function castToInt($x)
    {
        if (!is_numeric($x)) {
            $message = 'message:<<NO_CONVERTER_FOR_ARGUMENT_NUMBER integer.>>';
            throw new Exception($message);
        }
        return (int) $x;
    }

    public function getNull()
    {
        return null;
    }

    public function nullString()
    {
        return null;
    }

    public function echoInt($i)
    {
        return (int) $i;
    }

    public function echoString($s)
    {
        return PhpSlim_TypeConverter::toString($s);
    }
    
    public function echoBoolean($bool)
    {
        return PhpSlim_TypeConverter::toBool($bool);
    }

    public function oneString($string)
    {
        $this->value = (string) $string;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function oneInt($integer)
    {
        $this->value = (int) $integer;
    }

    public function oneDouble($double)
    {
        $this->value = (float) $double;
    }
    
    public function oneDate($date)
    {
        $this->oneString($date);
    }
    
    public function oneList($list)
    {
        $this->list = PhpSlim_TypeConverter::listToArray($list);
    }

    public function getListArg()
    {
        return $this->list;
    }

    public function setStringArray($array)
    {
        $this->stringArray = PhpSlim_TypeConverter::listToArray($array);
    }

    public function getStringArray()
    {
        return PhpSlim_TypeConverter::toString($this->stringArray);
    }

    public function setDoubleArray($array)
    {
        $array = PhpSlim_TypeConverter::listToArray($array);
        try {
            $result = array_map(array('self', 'castToFloat'), $array);
        } catch (Exception $e) {
            throw new Exception('message:<<CANT_CONVERT_TO_DOUBLE_LIST>>');
        }
        $this->doubleArray = $result;
    }

    public static function castToFloat($value)
    {
        if (!is_numeric($value)) {
            $message = 'message:<<NO_CONVERTER_FOR_ARGUMENT_NUMBER double.>>';
            throw new Exception($message);
        }
        return (float) $value;
    }

    public function getDoubleArray()
    {
        return PhpSlim_TypeConverter::toString($this->doubleArray);
    }

    public function manyArgs($int, $double, $char)
    {
        $this->intArg = (int) $int;
        $this->doubleArg = (float) $double;
        $this->charArg = $char;
    }

    public function getIntegerObjectArg()
    {
        return $this->intArg;
    }

    public function getDoubleObjectArg()
    {
        return $this->doubleArg;
    }

    public function getCharArg()
    {
        return $this->charArg;
    }

    public function triggerError()
    {
        return 1/0;
    }

    public function raiseStopException()
    {
        throw new PhpSlim_SlimError_StopTest('test stopped in TestSlim');
    }

    public function expect($method, $args, $return = null)
    {
        $this->_expectedMethod = $method;
        $this->_expectedArgs = $args;
        $this->_expectedReturn = $return;
    }

    public function noArgs()
    {
        $args = func_get_args();
        return $this->checkCall('noArgs', $args);
    }

    public function returnValue()
    {
        $args = func_get_args();
        return $this->checkCall('returnValue', $args);
    }

    public function oneArg()
    {
        $args = func_get_args();
        return $this->checkCall('oneArg', $args);
    }

    private function checkCall($method, $args)
    {
        if ($this->_expectedMethod == $method &&
                $this->_expectedArgs == $args) {
            $this->goodCall = true;
            return $this->_expectedReturn;
        }
    }

    public static function setStaticValue($value)
    {
        self::$_staticValue = $value;
    }

    public static function getStaticValue()
    {
        return self::$_staticValue;
    }

    public function nilad()
    {
        $this->niladWasCalled = true;
    }
    
    public function niladWasCalled()
    {
        return $this->niladWasCalled;
    }
}
