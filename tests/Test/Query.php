<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Test_Query extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;
    
    /**
     * @link http://www.ooyala.com/support/docs/backlot_api#signing
     */
    public function testSignatureGeneration()
    {
        $partnerCode = "lsNTrbQBqCQbH-VA6ALCshAHLWrV";
        $secretCode = "hn-Rw2ZH-YwllUYkklL5Zo_7lWJVkrbShZPb5CD1";
        
        $backlot = new \Phoo\Backlot($partnerCode, $secretCode);
        // Query: expires=1893013926, label[0]=any/some, statistics=1d,2d,7d,28d,30d,31d,lifetime, status=upl,live, and title=a
        $query = $backlot->query(array(
            'expires' => '1893013926',
            'label' => array('any/some'),
            'statistics' => '1d,2d,7d,28d,30d,31d,lifetime,',
            'status' => 'upl,live,',
            'title' => 'a'
        ));
        //$results = $backlot->fetch($query);
        
        $this->assertEquals($query->signature(), "dDiJo3LKLqPnqCpzEHDYBBNBe%2FmBgV3%2BVt9eiTgFYGk");
    }
}