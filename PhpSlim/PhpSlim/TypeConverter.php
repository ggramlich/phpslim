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
            return self::inspectArrayNoQuotes($object);
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
        $value = sprintf('%1.16F', $value);
        $value = self::stripTrailingNines($value);
        $value = self::stripTrailingZeros($value);
        if ((abs($value) < 1e-4) && (0 != $value)) {
            return self::scientificValue($value);
        }
        if ((abs($value) >= 1e7)) {
            return self::scientificValue($value);
        }
        return $value;
    }

    private static function stripTrailingZeros($value)
    {
        if (!self::hasLotsOfZeros($value) && (!self::hasTrailingZero($value))) {
            return $value;
        }
        if (!self::hasTrailingZero($value)) {
            $value = substr($value, 0, -5);
        }
        while (self::hasTrailingZero($value) && (substr($value, -2) !== '.0')) {
            $value = substr($value, 0, -1);
        }
        return $value;
    }

    private static function stripTrailingNines($value)
    {
        if (!self::hasLotsOfNines($value)) {
            return $value;
        }
        if (!self::hasTrailingNine($value)) {
            $value = substr($value, 0, -4);
        }
        while (self::hasTrailingNine($value)) {
            $value = substr($value, 0, -1);
        }
        $precision = strlen($value) - strpos($value, '.') - 1;
        $digit = ((float) ('1E-' . $precision));
        if ($value > 0) {
            $value = $value + $digit;
        } else {
            $value = $value - $digit;
        }
        return (string) $value;
    }

    private static function hasLotsOfZeros($value)
    {
        return false !== strpos($value, '000000000');
    }

    private static function hasTrailingZero($value)
    {
        return substr($value, -1) === '0';
    }

    private static function hasLotsOfNines($value)
    {
        return false !== strpos($value, '999999999');
    }

    private static function hasTrailingNine($value)
    {
        return substr($value, -1) === '9';
    }

    private static function scientificValue($value)
    {
        $precision = max(1, self::getScientificPrecision($value));
        return sprintf('%.' . $precision . 'E', $value);
    }

    private static function getScientificPrecision($value)
    {
        $digits = substr($value, strpos($value, '.') + 1);
        if (abs($value) < 1) {
            while ('0' === substr($digits, 0, 1)) {
                $digits = substr($digits, 1);
            }
        } else {
            while (is_numeric(substr($digits, 0, 1))) {
                $digits = substr($digits, 1);
            }
        }
        return strlen($digits) -1;
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

    public static function htmlTableToHash($html)
    {
        try {
            $doc = new DOMDocument();
            $doc->loadHTML($html);
            $doc->normalizeDocument();
            return self::domDocTableToHash($doc);
        } catch (Exception $e) {
            return array();
        }
    }

    private static function domDocTableToHash(DOMDocument $doc)
    {
        $hash = array();
        if (1 != $doc->getElementsByTagName('table')->length) {
            return array();
        }
        $table = $doc->getElementsByTagName('table')->item(0);
        for ($i = 0; $i < $table->childNodes->length; $i ++) {
            $row = $table->childNodes->item($i);
            $entry = self::getEntryFromRow($row);
            if (is_null($entry)) {
                return array();
            }
            list($key, $value) = $entry;
            $hash[$key] = $value;
        }
        return $hash;
    }

    private static function getEntryFromRow(DOMNode $row)
    {
        if ($row->nodeName != 'tr') {
            return;
        }
        $entry = array();
        for ($i = 0; $i < $row->childNodes->length; $i ++) {
            $element = $row->childNodes->item($i);
            if ($element->nodeType === XML_ELEMENT_NODE) {
                if ($element->nodeName != 'td') {
                    return;
                }
                $entry[] = $element->nodeValue;
            }
        }
        if (2 != count($entry)) {
            return;
        }
        return $entry;
    }
}
