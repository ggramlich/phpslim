<?php
class TestModule_TestFloatToString
{
    private $_float;
    
    public function setFloat($float)
    {
        $this->_float = (float) $float;
    }

    public function getFloat()
    {
        return PhpSlim_TypeConverter::floatToString($this->_float);
    }
    
}
