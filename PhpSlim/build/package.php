<?php
if (isset($_SERVER['argv'][2])) {
    $channel = $_SERVER['argv'][2];
} else {
    $channel = 'pear.php.net';
}

$api_version     = '1.0.1';
$release_version = '1.0.1';
$release_state   = 'stable';
$release_notes   = <<<EOT
More intelligent handling of some types.
Added JavaBridge code.
EOT;

require_once dirname(__FILE__) . '/../autoload.php';
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

$packageDirectory = realpath(dirname(__FILE__) . '/../');

$builder = new PhpSlimPackage_Builder($channel, $packageDirectory);
$director = new PhpSlimPackage_Director($builder);
$director->constructPackage();
$packageXml = $director->getPackageXml();

// Release data
$packageXml->setReleaseVersion($release_version);
$packageXml->setReleaseStability($release_state);
$packageXml->setAPIVersion($api_version);
$packageXml->setAPIStability($release_state);
$packageXml->setNotes($release_notes);

$packageXml->generateContents(); // create the <contents> tag

// 'php package.php make channelurl' will generate a package.xml file
// omitting the 'make' param will give you debug info
if (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make') {
    $packageXml->writePackageFile();
    file_put_contents('version.txt', 'version = ' . $release_version);
} else {
    $packageXml->debugPackageFile();
}

