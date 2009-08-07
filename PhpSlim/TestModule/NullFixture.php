<?php
class TestModule_NullFixture
{
    public function __construct($string = '')
    {
    }

    public function getNull()
    {
        return null;
    }

    public function getBlank()
    {
        return '';
    }
}
