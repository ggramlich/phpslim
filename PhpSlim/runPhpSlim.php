<?php
__PhpSlimBootStrap::loadBootStrap($_SERVER['argv']);
require_once dirname(__FILE__) . '/PhpSlim.php';
PhpSlim::main($_SERVER['argv']);

class __PhpSlimBootStrap
{
    public static function loadBootStrap($args)
    {
        $key = array_search('-b', $args);
        if (false === $key) {
            return;
        }
        if ($key > count($args) - 4) {
            die('The -b option must be followed by a filename '
                . "and preceed include path and port number.\n");
        }
        $bootstrapFile = realpath($args[$key + 1]);
        if (!is_readable($bootstrapFile)) {
            $message = sprintf(
                "The specified bootstrap file %s is not readable.\n",
                $bootstrapFile
            );
            die($message);
        }
        include $bootstrapFile;
    }
}
