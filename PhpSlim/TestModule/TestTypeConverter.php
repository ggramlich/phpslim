<?php
class TestModule_TestTypeConverter
{
    private $_float;
    private $_int;
    private $_bool;
    private $_string;
    
    public function setFloat($float)
    {
        $this->_float = (float) $float;
    }

    public function getFloat()
    {
        return $this->_float;
    }

    public function setInt($int)
    {
        $this->_int = (int) $int;
    }
    
    public function getInt()
    {
        return $this->_int;
    }
    
    public function setBool($bool)
    {
        $this->_bool = PhpSlim_TypeConverter::toBool($bool);
    }
    
    public function getBool()
    {
        return $this->_bool;
    }

    public function setString($string)
    {
        $this->_string = $string;
    }
    
    public function getString()
    {
        return $this->_string;
    }
}
