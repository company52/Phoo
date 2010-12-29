-<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
require dirname(__DIR__) . '/lib/Phoo/Autoloader.php';
$autoloader = new \Phoo\Autoloader();
$autoloader->register();


/**
 * Helper to get backlot object for use in tests
 * Used so credentials don't have to be repeted in all tests or globalized
 */
function phoo_backlot($partnerCode = "lsNTrbQBqCQbH-VA6ALCshAHLWrV", $secretCode = "hn-Rw2ZH-YwllUYkklL5Zo_7lWJVkrbShZPb5CD1") {
    $backlot = new \Phoo\Backlot($partnerCode, $secretCode);
    return $backlot;
}


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