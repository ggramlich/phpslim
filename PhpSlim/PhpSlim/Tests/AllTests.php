<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../../autoload.php';

class PhpSlim_Tests_AllTests
{
    private static $_tests = array(
        'ListSerializer',
        'ListDeserializer',
        'Statement',
        'StatementExecutor',
        'InstanceCreation',
        'MethodInvocation',
        'ListExecutor',
//        'SocketService',
        'TypeConverter',
    );

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PhpSlim Tests');
        self::addTests($suite);
        return $suite;
    }

    private static function addTests(PHPUnit_Framework_TestSuite $suite)
    {
        foreach (self::getTestClasses() as $class) {
            self::addTestClass($suite, $class);
        }
    }

    private static function getTestClasses()
    {
        $classes = array();
        foreach (self::$_tests as $test) {
            $classes[] = sprintf('PhpSlim_Tests_%sTest', $test);
        }
        return $classes;
    }

    private static function addTestClass(PHPUnit_Framework_TestSuite $suite,
                                            $class)
    {
        if (!class_exists($class)) {
            echo "UNKNOWN CLASS: $class\n";
        } else {
            $suite->addTestSuite($class);
        }
    }
}
