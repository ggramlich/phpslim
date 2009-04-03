<?php
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
        if (self::isNumericArray($object)) {
            return self::inspectArray($object);
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
        return print_r($object, true);
    }

    public static function inspectArray($array)
    {
        if (empty($array)) {
            return '[]';
        }
        return sprintf('["%s"]', implode('", "', $array));
    }

    private static function isNumericArray($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $lowerKeyArray = array_change_key_case($array, CASE_LOWER);
        $upperKeyArray = array_change_key_case($array, CASE_UPPER);
        return $lowerKeyArray == $upperKeyArray;
    }

    public static function floatToString($value)
    {
        $sign = ($value < 0)? '-': '';
        $value = abs($value);
        $int = floor($value);
        $fract = 10.0 * ($value - $int);
        $percent = substr((string) $fract, 2);
        $lotsOfSubsequentZeros = strpos($percent, '00000000000');
        if (false !== $lotsOfSubsequentZeros) {
            $percent = substr($percent, 0, $lotsOfSubsequentZeros);
        }
        return $sign . sprintf('%01d.%01d%s', $int, (int)$fract, $percent);
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
