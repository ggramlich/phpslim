<?php
echo "\n";
if (version_compare(PHP_VERSION, '5.1.2') === -1) {
    echo 'PhpSlim needs at least PHP version 5.1.2, installed version: ' .
        PHP_VERSION . "\nPlease upgrade your php installation.\n";
    exit;
}

$slimPath = dirname(__FILE__);
require_once $slimPath . '/autoload.php';

$wikiTests = $slimPath . '/FitNesseRoot/PhpTests';
$templateFile = $wikiTests . '/contentTemplate.txt';
$targetFile = $wikiTests . '/content.txt';
if (!file_exists($templateFile)) {
    echo 'Could not find ' . $templateFile . "\n";
    exit;
}
if (
    !is_writable($targetFile) && 
    (file_exists($targetFile) || !is_writable($wikiTests))
) {
    echo 'Cannot write to ' . $targetFile . "\n";
    exit;
}

$content = file_get_contents($templateFile);
$content = str_replace('%PHP_SLIM_PATH%', $slimPath, $content);
file_put_contents($targetFile, $content);
echo "Adapted $targetFile to your installation.\n";
echo "Configuration finished\n\n";
