<?php
class PhpSlim_ListSerializer
{
    /**
     * Get serialized version of the array
     *
     * @param array $list
     * @return string
     */
    public static function serialize(array $list)
    {
        $result = '[';
        $result .= self::lengthString(count($list));
        foreach ($list as $item) {
            if (is_null($item)) {
                $item = 'null';
            }
            if (is_array($item)) {
                $item = self::serialize($item);
            }
            $item = PhpSlim_TypeConverter::toString($item);
            $result .= self::lengthString(mb_strlen($item));
            $result .= $item . ':';
        }
        $result .= ']';
        return $result;
    }

    /**
     * Formatted length
     *
     * @param integer $length
     * @return string
     */
    private static function lengthString($length)
    {
        return sprintf('%06d:', $length);
    }
}
