<?php
class PhpSlim_ListDeserializer
{
    /**
     * Get deserialized array from the string
     *
     * @param $string
     * @return array
     * @throws PhpSlim_ListDeserializer_SyntaxError
     */
    public static function deserialize($string)
    {
        if (is_null($string)) {
            self::raise("Can't deserialize null");
        }
        if (empty($string)) {
            self::raise("Can't deserialize empty string");
        }
        if (substr($string, 0, 1) != '[') {
            self::raise("Serialized list has no starting [");
        }
        if (substr($string, -1) != ']') {
            self::raise("Serialized list has no ending ]");
        }
        $deserializer = new PhpSlim_ListDeserializer_Deserializer($string);
        return $deserializer->deserialize();
    }

    /**
     * @param string $message
     * @return void
     * @throws PhpSlim_ListDeserializer_SyntaxError
     */
    private static function raise($message)
    {
        throw new PhpSlim_ListDeserializer_SyntaxError($message);
    }
}
