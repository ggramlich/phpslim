<?php
abstract class PhpSlim_Tests_TestCase extends PHPUnit_Framework_TestCase
{
    protected function assertErrorMessage($message, $result)
    {
        $this->assertContains(
            PhpSlim::EXCEPTION_TAG . 'message:<<' . $message . '>>', $result
        );
    }
}
