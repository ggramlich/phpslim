<?php
class PhpSlim_Tests_ListSerializerTest extends PhpSlim_Tests_TestCase
{
    public function testSerializeAndEmptyList()
    {
        $this->assertSerializes(array(), '[000000:]');
    }

    public function testSerializeATwoItemList()
    {
        $expect = "[000002:000005:hello:000005:world:]";
        $this->assertSerializes(array("hello", "world"), $expect);
    }

    public function testSerializeAWordWithMultibyteCharacter()
    {
        $expect = "[000001:000001:ü:]";
        $this->assertSerializes(array("ü"), $expect);
    }

    public function testSerializeANestedList()
    {
        $expect = "[000001:000024:[000001:000007:element:]:]";
        $this->assertSerializes(array(array("element")), $expect);
    }

    public function testSerializeANestedListWithMultibyteCharacter()
    {
        $expect = "[000001:000024:[000001:000007:ülement:]:]";
        $this->assertSerializes(array(array("ülement")), $expect);
    }

    public function testSerializeAListWithANonString()
    {
        $this->assertSerializes(array(1), "[000001:000001:1:]");
    }

    public function testSerializeANullElement()
    {
        $this->assertSerializes(array(null), "[000001:000004:null:]");
    }

    private function assertSerializes(array $list, $expect)
    {
        $serialized = PhpSlim_ListSerializer::serialize($list);
        $this->assertEquals($expect, $serialized);
    }
}

