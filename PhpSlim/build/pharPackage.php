<?php
require_once 'PHP/Archive/Creator.php';

$creator = new PHP_Archive_Creator('runPhpSlim.php', 'phpslim.phar', 'gz');

$mainDir = realpath(dirname(__FILE__) . '/..');
$pharFile = $mainDir . '/../dist/phpslim.phar';
$creator->addFile($mainDir . '/PhpSlim.php', 'PhpSlim.php');
$creator->addFile($mainDir . '/runPhpSlim.php', 'runPhpSlim.php');
$creator->addDir($mainDir . '/PhpSlim', array('**/Tests/**', '**/Java/**'), array(), false, $mainDir);
$creator->useMD5Signature();

$creator->savePhar($pharFile);
