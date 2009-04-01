<?php
class TestModule_TestSlim
{
    public $goodCall = false;
    public $value;

    private $_expectedMethod;
    private $_expectedArgs;
    private $_expectedReturn;

    private $_integerArray;

    public function echoValue($x)
    {
        return $x;
    }

    public function returnString()
    {
        return 'string';
    }

    public function add($a, $b)
    {
        return $a . $b;
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

    private static function convertListToIntegerArray($list)
    {
        if ('[' == substr($list, 0, 1)) {
            $list = substr($list, 1);
        }
        if (']' == substr($list, -1)) {
            $list = substr($list, 0, -1);
        }
        $array = explode(',', $list);
        array_walk($array, 'trim');
        return array_map(array('self', 'castToInt'), $array);
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

    public function echoInt($i)
    {
        return $i;
    }

    public function echoString($s)
    {
        return $s;
    }

    public function triggerError()
    {
        return 1/0;
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
}
