<?php
require_once __DIR__ . '/init.php';

/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Phoo_Tests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Phoo Tests');

        // Traverse the "Test" directory and add the files as tests
        $path = dirname(__FILE__) . '/Test/';
        $dirIterator = new RecursiveDirectoryIterator($path);
        $Iterator = new RecursiveIteratorIterator($dirIterator);
        $tests = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        
        foreach($tests as $file) {
            $filename = current($file);
            require $filename;
            
            // Class file name by naming standards
            $fileClassName = substr(str_replace(DIRECTORY_SEPARATOR, '_', substr($filename, strlen($path))), 0, -4);
            $suite->addTestSuite('Test_'.$fileClassName);
        }
        return $suite;
    }
}