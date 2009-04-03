<?php
class PhpSlim_ListDeserializer_Deserializer
{
    /**
     * @var string
     */
    private $_string;

    /**
     * @var array
     */
    private $_list;

    /**
     * @var integer
     */
    private $_pos;

    /**
     * @param string $string
     * @return void
     */
    public function __construct($string)
    {
        $this->_string = $string;
    }

    /**
     * @return array
     */
    public function deserialize()
    {
        $this->_pos = 1;
        $this->_list = array();
        $numberOfItems = $this->getLength();
        for ($i = 0; $i < $numberOfItems; $i++) {
            $lengthOfItem = $this->getLength();
            $item = mb_substr($this->_string, $this->_pos, $lengthOfItem);
            $this->_pos += $lengthOfItem + 1;
            try {
                $sublist = PhpSlim_ListDeserializer::deserialize($item);
                $this->_list[] = $sublist;
            } catch (PhpSlim_ListDeserializer_SyntaxError $exception) {
                $this->_list[] = $item;
            }
        }
        return $this->_list;
    }

    /**
     * @return integer
     */
    private function getLength()
    {
        $length = mb_substr($this->_string, $this->_pos, 6);
        $this->_pos += 7;
        if (!is_numeric($length)) {
            $message = 'Wrong number format for length, read ' . $length;
            throw new PhpSlim_ListDeserializer_SyntaxError($message);
        }
        return (int) $length;
    }
}
