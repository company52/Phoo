<?php
namespace Phoo;

/**
 * Ooyala Backlot API wrapper
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Autoloader
{
    /**
     * Register as autoloader
     */
    public function register()
    {
        spl_autoload_register(array($this, 'load'));
    }
    
    
    /**
     * File autoloader for Phoo library
     */
    public function load($className)
    {
        // Don't attempt to autoload classes that are not 'Phoo' namespaced classes
        if(false === strpos($className, 'Phoo')) {
            return false;
        }
        $classFile = str_replace('\\', '/', $className);
        $classFile = str_replace('_', '/', $classFile);
        
        require dirname(__DIR__) . '/' . $classFile . '.php';
    }
}