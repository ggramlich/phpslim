<?php
class TestModule_MapInConstructor
{
    private $_map;

    public function __construct(array $map)
    {
        ksort($map);
        $this->_map = $map;
    }

    public function query()
    {
        return PhpSlim_TypeConverter::hashToPairs($this->_map);
    }
}
