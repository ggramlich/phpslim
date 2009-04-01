#!/usr/bin/php
<?php
$arguments = $_SERVER["argv"];
require_once dirname(__FILE__) . '/../../autoload.php';
$port = array_pop($arguments);

//error_log ("Running Port $port");

$service = new PhpSlim_SocketService('localhost', $port);
$service->init();
$service->write('Hello');
$service->close();

