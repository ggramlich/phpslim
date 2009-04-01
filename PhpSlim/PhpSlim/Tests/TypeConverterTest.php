<?php
class PhpSlim_Tests_TypeConverterTest extends PhpSlim_Tests_TestCase
{
    public function testFloatStringsAreNotSame()
    {
        $this->assertNotSame('0.1', '0.10');
        $this->assertNotSame('0.1', '.1');
    }

    public function testIntegers()
    {
        $this->assertFloatConvertsTo('0.0', 0);
        $this->assertFloatConvertsTo('1.0', 1);
        $this->assertFloatConvertsTo('-1.0', -1);
        $this->assertFloatConvertsTo('123.0', 123);
        $this->assertFloatConvertsTo('-123.0', -123);
    }

    public function testOneDigitAfterDot()
    {
        $this->assertFloatConvertsTo('0.1', .1);
        $this->assertFloatConvertsTo('1.1', 1.100);
        $this->assertFloatConvertsTo('-1.1', -1.1);
        $this->assertFloatConvertsTo('0.9', .9);
        $this->assertFloatConvertsTo('1.9', 1.9);
        $this->assertFloatConvertsTo('-1.9', -1.9);
        $this->assertFloatConvertsTo('123.9', 123.9);
        $this->assertFloatConvertsTo('-123.9', -123.9);
    }

    public function testZeroAfterDot()
    {
        $this->assertFloatConvertsTo('0.01', .01);
        $this->assertFloatConvertsTo('1.01', 1.0100);
        $this->assertFloatConvertsTo('-1.01', -1.01);
        $this->assertFloatConvertsTo('0.09', .09);
        $this->assertFloatConvertsTo('1.09', 1.09);
        $this->assertFloatConvertsTo('-1.09', -1.09);
        $this->assertFloatConvertsTo('123.09', 123.09);
        $this->assertFloatConvertsTo('-123.09', -123.09);
    }

    public function testArbitraryNumbers()
    {
        $this->assertFloatConvertsTo('12.3456', 12.3456);
        $this->assertFloatConvertsTo('33.33333', 33.33333);
        $this->assertFloatConvertsTo('-33.33333', -33.33333);
    }

    public function testHashToPairsEmpty()
    {
        $this->assertHashConvertsToPairs(array(), array());
    }

    public function testHashToPairsOneEntry()
    {
        $expected = array(array('a', 'x'));
        $this->assertHashConvertsToPairs($expected, array('a' => 'x'));
    }

    public function testHashToPairsThreeEntries()
    {
        $hash = array('a' => 'x', 'b' => 'y', 'c' => 'z');
        $expected = array(
            array('a', 'x'),
            array('b', 'y'),
            array('c', 'z')
        );
        $this->assertHashConvertsToPairs($expected, $hash);
    }

    public function testEmptyObjectToPairs()
    {
        $object = new StdClass();
        $pairs = PhpSlim_TypeConverter::objectToPairs($object);
        $this->assertSame(array(), $pairs);
    }

    public function testObjectToPairs()
    {
        $object = new StdClass();
        $object->a = 'x';
        $object->b = 'y';
        $expected = array(
            array('a', 'x'),
            array('b', 'y'),
        );
        $pairs = PhpSlim_TypeConverter::objectToPairs($object);
        $this->assertSame($expected, $pairs);
    }
    

    private function assertFloatConvertsTo($expected, $value)
    {
        $string = PhpSlim_TypeConverter::floatToString($value);
        $this->assertSame($expected, $string);
    }

    private function assertHashConvertsToPairs($expected, $hash)
    {
        $pairs = PhpSlim_TypeConverter::hashToPairs($hash);
        $this->assertSame($expected, $pairs);
    }
}

