<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Test_Backlot extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;
    
    /**
     * @link http://www.ooyala.com/support/docs/backlot_api#signing
     */
    public function testBacklotSignatureGeneration()
    {
        $partnerCode = "lsNTrbQBqCQbH-VA6ALCshAHLWrV";
        $secretCode = "hn-Rw2ZH-YwllUYkklL5Zo_7lWJVkrbShZPb5CD1";
        
        $backlot = new \Phoo\Backlot($partnerCode, $secretCode);
        $this->assertEquals($backlot->signature(), "dDiJo3LKLqPnqCpzEHDYBBNBe%2FmBgV3%2BVt9eiTgFYGk");
    }
}