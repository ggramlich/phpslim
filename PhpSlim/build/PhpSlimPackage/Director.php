<?php
class PhpSlimPackage_Director
{
    private $_packageBuilder;
    private $_packageXml;

    public function __construct(PhpSlimPackage_Builder $packageBuilder)
    {
        $this->_packageBuilder = $packageBuilder;
    }

    public function getPackageXml()
    {
        return $this->_packageXml;
    }

    public function constructPackage()
    {
        $this->initPackageXml();
        $packageBuilder = $this->_packageBuilder;
        $packageBuilder->setPackageXml($this->getPackageXml());
        $packageBuilder->buildOptions();
        $packageBuilder->buildIgnore();
        $packageBuilder->buildInclude();
        $packageBuilder->buildInfo();
        $packageBuilder->buildDependencies();
        $packageBuilder->buildMaintainer();
        $packageBuilder->buildReplacements();
        $packageBuilder->buildWindowsRelease();
        $packageBuilder->buildLinuxRelease();
    }

    private function initPackageXml()
    {
        $this->_packageXml = new PEAR_PackageFileManager2;
    }

}
