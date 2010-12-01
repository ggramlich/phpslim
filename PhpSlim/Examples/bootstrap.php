<?php
class __MyBootstrap
{
    function loadExample($className)
    {
        // Allows to load Examples_ classes without import table
        // (not recommended, just an example for a custom bootstrap)
        if (strpos($className, 'Examples_') === 0) {
            $className = substr($className, 9);
        }
        
        $fileName = strtr($className, '_', DIRECTORY_SEPARATOR) . '.php';
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($path)) {
            include $path;
        }
    }
}

spl_autoload_register(array('__MyBootstrap', 'loadExample'));
