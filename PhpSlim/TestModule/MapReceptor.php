<?php
class TestModule_MapReceptor
{
    private $_map;

    public function setMap(array $map)
    {
        ksort($map);
        $this->_map = $map;
        return true;
    }

    public function query()
    {
        return PhpSlim_TypeConverter::hashToPairs($this->_map);
    }
}
