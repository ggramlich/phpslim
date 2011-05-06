<?php
class PhpSlimPackage_Builder
{
    public $channel;
    public $packageXml;
    public $packageDirectory;

    public $package = 'PhpSlim';
    public $summary = 'Php port of Slim';
    public $license = 'PHP License';
    public $licenseUrl = 'http://www.php.net/license';

    public $phpDep = '5.1.2';
    public $pearinstallerDep = '1.8.0';
    public $extensionDeps = array(
        'mbstring' => 'required',
        'pcre' => 'required',
        'SPL' => 'required',
        'sockets' => 'required',
    );

    public $include = array(
        'PhpSlim.php',
        'PhpSlim/*',
        'script/*',
        'README',
    );

    public $ignore = array(
        'FitNesseRoot/*',
        'PhpSlim/Tests/*'
    );

    public $maintainer = array(
        'lead',
        'ggramlich',
        'Gregor Gramlich',
        'gramlich@eosc.de'
    );

    // Must override this in the concrete classes
    public $apiVersion;
    public $releaseVersion;
    public $releaseState;

    public function __construct($channel, $packageDirectory)
    {
        $this->channel = $channel;
        $this->packageDirectory = $packageDirectory;
    }

    public function setPackageXml($packageXml)
    {
        $this->packageXml = $packageXml;
        $this->packageXml->setPackageType('php');
    }

    public function getPackageXml()
    {
        return $this->packageXml;
    }

    public function buildOptions()
    {
        $this->packageXml->setOptions($this->getOptions());
    }

    public function buildIgnore()
    {
        $this->packageXml->addIgnore($this->ignore);
    }

    public function buildInclude()
    {
        $this->packageXml->addInclude($this->include);
    }

    public function buildInfo()
    {
        $packageXml = $this->packageXml;
        $packageXml->setPackage($this->package);
        $packageXml->setSummary($this->summary);
        $packageXml->setDescription($this->getDescription());
        $packageXml->setLicense($this->license, $this->licenseUrl);
        $packageXml->setChannel($this->channel);
    }

    public function buildDependencies()
    {
        $packageXml = $this->packageXml;
        $packageXml->setPhpDep($this->phpDep);
        $packageXml->setPearinstallerDep($this->pearinstallerDep);
        foreach ($this->extensionDeps as $extension => $type) {
            $packageXml->addExtensionDep($type, $extension);
        }
    }

    public function buildMaintainer()
    {
        $callback = array($this->packageXml, 'addMaintainer');
        call_user_func_array($callback, $this->maintainer);
    }

    public function buildReplacements()
    {
        $this->changeToPackageDirectory();
        $packageXml = $this->packageXml;
        $packageXml->addReplacement('script/pear-runPhpSlim', 'pear-config', '@PHP-BIN@', 'php_bin');
        $packageXml->addReplacement('script/pear-runPhpSlim.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
        $packageXml->addReplacement('script/pear-runPhpSlim.bat', 'pear-config', '@BIN-DIR@', 'bin_dir');
    }

    private function changeToPackageDirectory()
    {
        // addReplacement() uses glob and thus we have to be
        // in the packagedirectory
        chdir($this->packageDirectory);
    }

    public function buildWindowsRelease()
    {
        $packageXml = $this->packageXml;
        $packageXml->addRelease(); // set up a release section
        $packageXml->setOSInstallCondition('windows');
        $packageXml->addWindowsEol('script/pear-runPhpSlim.bat');
        $packageXml->addInstallAs('script/pear-runPhpSlim.bat', 'runPhpSlim.bat');
        $packageXml->addInstallAs('script/pear-runPhpSlim', 'runPhpSlim');
        // these next few files are only used if the archive is extracted as-is
        // without installing via "pear install blah"
        $packageXml->addIgnoreToRelease('script/runPhpSlim');
        $packageXml->addIgnoreToRelease('script/runPhpSlim.bat');
    }

    public function buildLinuxRelease()
    {
        $packageXml = $this->packageXml;
        $packageXml->addRelease(); // add another release section for all other OSes
        $packageXml->addUnixEol('script/pear-runPhpSlim');
        $packageXml->addInstallAs('script/pear-runPhpSlim', 'runPhpSlim');
        $packageXml->addIgnoreToRelease('script/pear-runPhpSlim.bat');
        // these next few files are only used if the archive is extracted as-is
        // without installing via "pear install blah"
        $packageXml->addIgnoreToRelease('script/runPhpSlim');
        $packageXml->addIgnoreToRelease('script/runPhpSlim.bat');
    }
    //    public function getChannel()
//    {
//        return $this->channel;
//    }
//
//    public function getApiVersion()
//    {
//        return $this->getAbstractAttribute('apiVersion');
//    }
//
//    public function getReleaseVersion()
//    {
//        return $this->getAbstractAttribute('releaseVersion');
//    }
//
//    public function getReleaseState()
//    {
//        return $this->getAbstractAttribute('releaseState');
//    }
//
////    abstract public function getReleaseNotes();
//
    public function getDescription()
    {
        return <<<EOT
PhpSlim is the PHP port of Robert C. Martin's Slim
(Simple List Invocation Method).

It is written and maintained by Gregor Gramlich.

You need a copy of FitNesse to run the FitNesse wiki.
Download the latest release (use the zip file) from
http://fitnesse.org/FitNesseDevelopment.DownLoad
EOT;
//' Eclipse highlighting gets confused by the apostrophe in heredoc.
    }

    public function getOptions()
    {
        return array(
            'baseinstalldir' => '/',
            'packagedirectory' => $this->getPackageDirectory(),
            'dir_roles' => array('script' => 'script'), // not data
            'exceptions' => array(
                'README' => 'doc', // not src files
            )
        );
    }

//    public function getIgnore()
//    {
//        return $this->ignore;
//    }
//
//    public function getInclude()
//    {
//        return $this->include;
//    }
//
//    public function getLicense()
//    {
//        return $this->license;
//    }
//
//    public function getLicenseUrl()
//    {
//        return $this->licenseUrl;
//    }
//
//    private function getAbstractAttribute($attribute)
//    {
//        if (empty($this->$attribute)) {
//            $message = 'Extended class does not set attribute %s.';
//            throw new Exception (sprintf($message, $attribute));
//        }
//        return $this->$attribute;
//    }

    private function getPackageDirectory()
    {
        return $this->packageDirectory;
    }
}
