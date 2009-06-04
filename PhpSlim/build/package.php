<?php

require_once('PEAR/PackageFileManager2.php');

PEAR::setErrorHandling(PEAR_ERROR_DIE);

date_default_timezone_set('Europe/Berlin');

// Set common variables
$package         = 'PhpSlim';
$summary         = 'Php port of Slim';
$description     = <<<EOT
PhpSlim is the PHP port of Robert C. Martin's Slim
(Simple List Invocation Method).

It is written and maintained by Gregor Gramlich.

You need a copy of FitNesse to run the FitNesse wiki.
Download the latest release (use the zip file) from
http://fitnesse.org/FitNesseDevelopment.DownLoad
EOT;
//' Eclipse highlighting got confused by the apostrophe in heredoc.

$channel         = 'ggramlich.github.com';
$channel         = 'pear.php.net';
$api_version     = '1.0.0';
$release_version = '1.0.0';
$release_state   = 'stable';
$release_notes   = <<<EOT
First release of PhpSlim as PEAR package.
EOT;

$packagedirectory = realpath(dirname(__FILE__) . '/../');

$packagexml = new PEAR_PackageFileManager2;
$e = $packagexml->setOptions(
    array(
        'baseinstalldir' => '/',
        'packagedirectory' => $packagedirectory,
             'dir_roles' => array('script' => 'script'), // not data
        'exceptions' => array(
            'README' => 'doc', // not src files
        )
    ));

$packagexml->addIgnore(
    array('FitNesseRoot/*', 'PhpSlim/Tests/*')
);

$packagexml->addInclude(
    array(
        'PhpSlim.php',
        'PhpSlim/*',
        'script/*',
        'README',
    )
);

// Set info about the the package
$packagexml->setPackage($package);
$packagexml->setSummary($summary);
$packagexml->setDescription($description);
$packagexml->setLicense('PHP License', 'http://www.php.net/license');
$packagexml->setChannel($channel);

// Release data
$packagexml->setReleaseVersion($release_version);
$packagexml->setReleaseStability($release_state);
$packagexml->setAPIVersion($api_version);
$packagexml->setAPIStability($release_state);
$packagexml->setNotes($release_notes);

// Dependencies
//$packagexml->clearDeps();
$packagexml->setPhpDep('5.1.2');
$packagexml->setPearinstallerDep('1.8.0');
$packagexml->addExtensionDep('required', 'mbstring');
$packagexml->addExtensionDep('required', 'pcre');
$packagexml->addExtensionDep('required', 'SPL');
$packagexml->addExtensionDep('required', 'sockets');

// Maintainer(s)
$packagexml->addMaintainer('lead',
                           'ggramlich',
                           'Gregor Gramlich',
                           'gramlich@eosc.de');

// General
$packagexml->setPackageType('php'); // written in PHP

// addReplacement() uses glob and thus we have to be in the packagedirectory
chdir($packagedirectory);
$packagexml->addReplacement('script/pear-runPhpSlim', 'pear-config', '@PHP-BIN@', 'php_bin');
$packagexml->addReplacement('script/pear-runPhpSlim.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
$packagexml->addReplacement('script/pear-runPhpSlim.bat', 'pear-config', '@BIN-DIR@', 'bin_dir');
$packagexml->addWindowsEol('script/pear-runPhpSlim.bat');
$packagexml->addUnixEol('script/pear-runPhpSlim');

$packagexml->addRelease(); // set up a release section
$packagexml->setOSInstallCondition('windows');
$packagexml->addInstallAs('script/pear-runPhpSlim.bat', 'runPhpSlim.bat');
$packagexml->addInstallAs('script/pear-runPhpSlim', 'runPhpSlim');
// these next few files are only used if the archive is extracted as-is
// without installing via "pear install blah"
$packagexml->addIgnoreToRelease('script/runPhpSlim');
$packagexml->addIgnoreToRelease('script/runPhpSlim.bat');


$packagexml->addRelease(); // add another release section for all other OSes
$packagexml->addInstallAs('script/pear-runPhpSlim', 'runPhpSlim');
$packagexml->addIgnoreToRelease('script/pear-runPhpSlim.bat');
// these next few files are only used if the archive is extracted as-is
// without installing via "pear install blah"
$packagexml->addIgnoreToRelease('script/runPhpSlim');
$packagexml->addIgnoreToRelease('script/runPhpSlim.bat');




$packagexml->generateContents(); // create the <contents> tag


// get a PEAR_PackageFile object
//$pkg = &$packagexml->exportCompatiblePackageFile1();

// 'php package.php make' will generate a package.xml file
// omitting the 'make' param will give you debug info
if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
//    $pkg->writePackageFile();
    $packagexml->writePackageFile();
} else {
//    $pkg->debugPackageFile();
    $packagexml->debugPackageFile();
}

?>
