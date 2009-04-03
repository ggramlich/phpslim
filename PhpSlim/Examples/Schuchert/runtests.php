<?php
// Run the unit test described in
// http://schuchert.wikispaces.com/FitNesse.Tutorials.2
require_once dirname(__FILE__) . '/Tests/AllTests.php';
//require_once dirname(__FILE__) . '/../../autoload.php';

$runner = new PHPUnit_TextUI_TestRunner;
$result = $runner->doRun(Schuchert_Tests_AllTests::suite());
