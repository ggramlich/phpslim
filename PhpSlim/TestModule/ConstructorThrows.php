<?php
class TestModule_ConstructorThrows
{
    public function __construct($message)
    {
        throw new Exception($message);
    }
}
