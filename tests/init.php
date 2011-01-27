<?php
// Set TZ to CST for tests
date_default_timezone_set('America/Chicago');

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
function phoo_api($api, $partnerCode = "lsNTrbQBqCQbH-VA6ALCshAHLWrV", $secretCode = "hn-Rw2ZH-YwllUYkklL5Zo_7lWJVkrbShZPb5CD1") {
    // API name
    switch($api)
    {
        case 'backlot':
            $api = new \Phoo\Backlot($partnerCode, $secretCode);
        break;
    
        case 'ingestion':
            $api = new \Phoo\Ingestion($partnerCode, $secretCode);
        break;
    
        case 'analytics':
            $api = new \Phoo\Analytics($partnerCode, $secretCode);
        break;
    }
    
    return $api;
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