<?php
class CodeFilterIterator extends FilterIterator
{
    public function accept()
    {
        $path = $this->getInnerIterator()->current()->getPath();
        return (preg_match('([/\\\\](Tests|Java)[/\\\\])', $path) === 0);
    }
}

$mainDir  = realpath(dirname(__FILE__) . '/..');
$pharFile = realpath($mainDir . '/../dist') . '/phpslim.phar';

$files = new CodeFilterIterator(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($mainDir . '/PhpSlim')
    )
);

$phar = new Phar($pharFile, 0, 'phpslim.phar');
$phar->buildFromIterator($files, $mainDir);
$phar->addFile($mainDir . '/PhpSlim.php', 'PhpSlim.php');
$phar->addFile($mainDir . '/runPhpSlim.php', 'index.php');
$phar->setDefaultStub('index.php', 'index.php');
$phar->compressFiles(Phar::GZ);
$phar->setSignatureAlgorithm(Phar::SHA1);
