<?php
class PhpSlim_Tests_ListDeserializerTest extends PhpSlim_Tests_TestCase
{
    private $_list;

    public function setup()
    {
        $this->_list = array();
    }

    /**
     * @expectedException PhpSlim_ListDeserializer_SyntaxError
     */
    public function testExceptionDeserializeNullString()
    {
        $this->deserialize(null);
    }

    /**
     * @expectedException PhpSlim_ListDeserializer_SyntaxError
     */
    public function testExceptionDeserializeEmptyString()
    {
        $this->deserialize('');
    }

    /**
     * @expectedException PhpSlim_ListDeserializer_SyntaxError
     */
    public function testExceptionDeserializeStringThatDoesNotStartWithABracket()
    {
        $this->deserialize('hello');
    }

    /**
     * @expectedException PhpSlim_ListDeserializer_SyntaxError
     */
    public function testExceptionDeserializeStringThatDoesNotEndWithABracket()
    {
        $this->deserialize('[000000:');
    }

    public function testDeserializeAnEmptyList()
    {
        $this->check();
    }

    public function testDeserializeAListWithOneInteger()
    {
        $this->_list = array(1);
        $this->check();
    }

    public function testDeserializeAListWithBrackets()
    {
        $this->_list = array('[1]');
        $this->check();
    }

    public function testDeserializeAListWithColon()
    {
        $this->_list = array('a:b');
        $this->check();
    }

    public function testDeserializeAListWithOneElement()
    {
        $this->_list = array('hello');
        $this->check();
    }

    public function testDeserializeAListWithTwoElements()
    {
        $this->_list = array('hello', 'bob');
        $this->check();
    }

    public function testDeserializeSublists()
    {
        $this->_list = array('hello', array('bob', 'micah'), 'today');
        $this->check();
    }

    private function check()
    {
        $serialized = $this->serialize($this->_list);
        $deserialized = $this->deserialize($serialized);
        $this->assertEquals($this->_list, $deserialized);
    }

    private function deserialize($string)
    {
        return PhpSlim_ListDeserializer::deserialize($string);
    }

    private function serialize(array $list)
    {
        return PhpSlim_ListSerializer::serialize($list);
    }
}
