<?php
class PhpSlim_SymbolRepository
{
    private $_symbols = array();

    public function setSymbol($name, $value)
    {
        $this->_symbols[$name] = $value;
    }

    public function getSymbol($name)
    {
        if (!$this->isSymbolSet($name)) {
            return null;
        }
        return $this->_symbols[$name];
    }

    private function isSymbolSet($name)
    {
        return array_key_exists($name, $this->_symbols);
    }

    public function replaceSymbols(array $list)
    {
        foreach ($list as $key => $item) {
            if (is_array($item)) {
                $list[$key] = $this->replaceSymbols($item);
            } else {
                $list[$key] = $this->replaceSymbolsInItem($item);
            }
        }
        return $list;
    }

    private function replaceSymbolsInItem($item)
    {
        if ($this->itemIsSymbol($item)) {
            // Single symbol, don't replace within string, can return object
            return $this->getSymbol(substr($item, 1));
        }
        $symbolKeys = array_keys($this->_symbols);
        $search = array_map(array($this, 'prependDollar'), $symbolKeys);
        // I tried array_map for the following, but I got a warning.
        $replaceStrings = array();
        foreach ($this->_symbols as $symbolValue) {
            $replaceStrings[] = PhpSlim_TypeConverter::toString($symbolValue);
        }
        return str_replace($search, $replaceStrings, $item);
    }

    private function itemIsSymbol($item)
    {
        if ('$' != substr($item, 0, 1)) {
            return false;
        }
        return $this->isSymbolSet(substr($item, 1));
    }

    private function prependDollar($key)
    {
        return '$' . $key;
    }
}
