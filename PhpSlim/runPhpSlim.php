<?php
mb_internal_encoding("UTF-8");
$arguments = $_SERVER["argv"];
$port = array_pop($arguments);
$path = array_pop($arguments);
set_include_path($path . PATH_SEPARATOR . get_include_path());

require_once dirname(__FILE__) . '/autoload.php';

$slimServer = new PhpSlim_Server();
//$slimServer->setLogger(new PhpSlim_Logger_Echo(), new PhpSlim_Logger_Error());
$slimServer->run($port);
