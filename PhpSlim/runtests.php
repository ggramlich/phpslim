<?php
error_reporting(E_ALL | E_STRICT);
require_once dirname(__FILE__) . '/autoload.php';

$runner = new PHPUnit_TextUI_TestRunner;
$result = $runner->doRun(PhpSlim_Tests_AllTests::suite());
if (!($result->noneSkipped() && $result->wasSuccessful())) {
    exit(1);
}

// Check that we are still E_STRICT
if (error_reporting() !== (E_ALL | E_STRICT)) {
    echo "Warning: E_STRICT compliance was turned off during tests.\n";
    exit(1);
}
