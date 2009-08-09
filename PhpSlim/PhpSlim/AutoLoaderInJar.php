<?php
class PhpSlim_AutoLoaderInJar extends PhpSlim_AutoLoader
{
    private static $_instance;

    public static function singleton()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new PhpSlim_AutoLoaderInJar();
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

    protected function ensureMyClassIsOnPath($path)
    {
    }
}
