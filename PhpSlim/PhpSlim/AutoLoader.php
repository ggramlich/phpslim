<?php
class PhpSlim_AutoLoader
{
    /**
     * @var PhpSlim_AutoLoader
     */
    private static $_instance;

    /**
     * @var boolean
     */
    private $_registered = false;

    /**
     * @var array
     */
    private $_loadedClasses = array();

    private function __construct()
    {
    }

    public static function singleton()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new PhpSlim_AutoLoader();
        }
        return self::$_instance;
    }

    /**
     * Register the autoload using the singleton
     *
     * @return void
     */
    public static function start()
    {
        self::singleton()->registerAutoLoad();
        self::cleanupIncludePath();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function registerAutoLoad()
    {
        if ($this->_registered) {
            return;
        }
        $this->ensureIncludePath();
        $success = spl_autoload_register(array($this, 'autoload'));
        if (!$success) {
            throw new Exception('Could not register autoload.');
        }
        $this->_registered = true;
    }

    private static function cleanupIncludePath()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        $trimmedPaths = array_map(array('self', 'trimPath'), $paths);
        $uniquePaths = array_unique($trimmedPaths);
        set_include_path(implode(PATH_SEPARATOR, $uniquePaths));
    }

    private static function trimPath($path)
    {
        return rtrim($path, '\\/');
    }

    /**
     * Ensure that this file is on the include path
     *
     * Sets the include path automatically, if necessary.
     *
     * @return void
     * @throws Exception
     */
    private function ensureIncludePath()
    {
        // check, if my own class definition is loadable
        $path = $this->getPath(__CLASS__) . '.php';
        if (false === self::getIncludableFile($path)) {
            $basePath = realpath(dirname(__FILE__) . '/..');
            $newPath = get_include_path() . PATH_SEPARATOR . $basePath;
            set_include_path($newPath);
        }
        if (false === self::getIncludableFile($path)) {
            throw new Exception('Cannot set include path');
        }
    }

    /**
     * @param string $class
     * @return void
     */
    public function autoload($class)
    {
        if ($this->classLoaded($class)) {
            // Do not attempt to reload a class.
            // This is especially if the load failed before,
            // otherwise autoload should not be called.
            return;
        }
        if ($path = $this->getFullPathForClass($class)) {
            include_once $path;
        }
        $this->registerClassLoaded($class);
    }

    /**
     * @param string $class
     * @return string
     */
    private static function getFullPathForClass($class)
    {
        $path = self::getPath($class);
        $file = $path . '.php';
        return self::getIncludableFile($file);
    }

    /**
     * @param string $class
     * @return string
     */
    private static function getPath($class)
    {
        return str_replace('_', '/', $class);
    }

    /**
     * @param string $class
     * @return void
     */
    private function registerClassLoaded($class)
    {
        $this->_loadedClasses[] = $class;
    }

    /**
     * @param string $class
     * @return boolean
     */
    private function classLoaded($class)
    {
        return in_array($class, $this->_loadedClasses);
    }

    /**
     * Find appropriate file within include path
     *
     * Returns an absolute path
     *
     * @param string $file
     * @return string
     */
    private static function getIncludableFile($file)
    {
        if (file_exists($file)) {
            return realpath($file);
        }

        $paths = explode(PATH_SEPARATOR, get_include_path() . PATH_SEPARATOR);

        foreach ($paths as $path) {
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;
            if (file_exists($fullpath)) {
                return $fullpath;
            }
        }
        return false;
    }

}
