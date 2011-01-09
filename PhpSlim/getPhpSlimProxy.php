<?php
define('ENGINE_SCOPE', 100);
define('PHP_VAR_PATH', 'PHP_PATH');
define('PHP_VAR_PROXY', 'phpProxy');

$myPath = java_context()->getAttribute(PHP_VAR_PATH, ENGINE_SCOPE);

set_include_path($myPath . PATH_SEPARATOR . get_include_path());
if (!class_exists('PhpSlim_AutoLoaderInJar', false)) {
    require_once 'PhpSlim/AutoLoaderInJar.php';
}
PhpSlim_AutoLoaderInJar::start();

$javaProxy = java_closure(new PhpSlim_Java_Proxy());
java_context()->setAttribute(PHP_VAR_PROXY, $javaProxy, ENGINE_SCOPE);
java_context()->call(java_closure());

//