-<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
require dirname(__DIR__) . '/lib/Phoo/Autoloader.php';
$autoloader = new \Phoo\Autoloader();
$autoloader->register();


/**
 * Autoload test classes
 */
function test_phoo_autoloader($className) {
    // Don't attempt to autoload 'PHPUnit_' or 'Phoo' namespaced classes
    if(false !== strpos($className, 'PHPUnit_') || false !== strpos($className, 'Phoo')) {
        return false;
    }
    $classFile = str_replace('_', '/', $className) . '.php';
    require __DIR__ . '/' . $classFile;
}
spl_autoload_register('test_phoo_autoloader');