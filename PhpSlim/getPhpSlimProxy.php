<?php
define('ENGINE_SCOPE', 100);
define('PHP_VAR_PATH', 'PHP_PATH');
define('PHP_VAR_PROXY', 'phpProxy');

$myPath = java_context()->getAttribute(PHP_VAR_PATH, ENGINE_SCOPE);

set_include_path($myPath . PATH_SEPARATOR . get_include_path());
require_once 'PhpSlim/AutoLoader.php';
PhpSlim_AutoLoader::start();

java_context()->setAttribute(PHP_VAR_PROXY, java_closure(new PhpSlim_Java_Proxy()), ENGINE_SCOPE);
java_context()->call(java_closure());

//