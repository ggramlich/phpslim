<?php
$examplesPath = realpath(dirname(__FILE__) . '/../../');
set_include_path(get_include_path() . PATH_SEPARATOR . $examplesPath);
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../../../autoload.php';

class Schuchert_Tests_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('DVR Tests');
        $suite->addTestSuite('Schuchert_Tests_SeasonPassManagerTest');
        return $suite;
    }
}
