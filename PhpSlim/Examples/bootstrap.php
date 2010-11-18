<?php
function bootstrap($className)
{
    if (strpos($className, 'Examples_') === 0) {
        $className = substr($className, 9);
    }
    
    $fileName = dirname(__FILE__) . '/' . strtr($className, '_', '/') . '.php';
    if (file_exists($fileName)) {
        include $fileName;
    }
}

spl_autoload_register('bootstrap');