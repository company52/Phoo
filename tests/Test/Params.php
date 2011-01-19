<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Test_Params extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;
    
    /**
     * Example API credentials and resutling valid signature from Ooyala Backlot API docs
     * 
     * @link http://www.ooyala.com/support/docs/backlot_api#signing
     */
    public function testSignatureGeneration()
    {
        $partnerCode = "lsNTrbQBqCQbH-VA6ALCshAHLWrV";
        $secretCode = "hn-Rw2ZH-YwllUYkklL5Zo_7lWJVkrbShZPb5CD1";
        
        $backlot = new \Phoo\Backlot($partnerCode, $secretCode);
        
        // Example Query Given from Docs:
        //   expires=1893013926, label[0]=any/some, statistics=1d,2d,7d,28d,30d,31d,lifetime, status=upl,live, and title=a
        $params = $backlot->params(array(
            'expires' => '1893013926',
            'label' => array('any/some'),
            'statistics' => '1d,2d,7d,28d,30d,31d,lifetime,',
            'status' => 'upl,live,',
            'title' => 'a'
        ));
        
        $this->assertEquals($params->signature(), "dDiJo3LKLqPnqCpzEHDYBBNBe%2FmBgV3%2BVt9eiTgFYGk");
    }
    
    
    /**
     * Example full API URL generated with all params and necessary pcode and signature hash
     * 
     * @link http://www.ooyala.com/support/docs/backlot_api#signing
     */
    public function testParamsToString()
    {
        $backlot = phoo_backlot("lsNTrbQBqCQbH-VA6ALCshAHLWrV", "hn-Rw2ZH-YwllUYkklL5Zo_7lWJVkrbShZPb5CD1");
        $params = $backlot->params(array(
            'expires' => '1893013926',
            'label' => array('any/some'),
            'statistics' => '1d,2d,7d,28d,30d,31d,lifetime,',
            'status' => 'upl,live,',
            'title' => 'a'
        ));
        
        $this->assertEquals("pcode=lsNTrbQBqCQbH-VA6ALCshAHLWrV&expires=1893013926&label%5B0%5D=any%2Fsome&statistics=1d%2C2d%2C7d%2C28d%2C30d%2C31d%2Clifetime%2C&status=upl%2Clive%2C&title=a&signature=dDiJo3LKLqPnqCpzEHDYBBNBe%2FmBgV3%2BVt9eiTgFYGk", $params->queryString());
    }
    
    /**
     * Tests that the generated query string will automatically include the pcode and full signature without having to put them in the query
     * Should not throw an exception from \Phoo\Params::checkRequiredParams()
     */
    public function testParamsSignatureAndPcodeAreIncludedByDefault()
    {
        $backlot = phoo_backlot();
        $params = $backlot->params(array(
            'title' => 'test'
        ))->required(array('pcode', 'signature'));
        $qs = $params->queryString();
        
        $qp = array();
        parse_str($qs, $qp);
        
        $this->assertArrayHasKey('pcode', $qp);
        $this->assertArrayHasKey('signature', $qp);
    }
    
    /**
     * Missing required parameters should throw an exception from \Phoo\Params::checkRequiredParams()
     * 
     * @expectedException \UnexpectedValueException
     */
    public function testParamsMissingRequiredThrowsException()
    {
        $backlot = phoo_backlot();
        $params = $backlot->params(array(
            'title' => 'test'
        ))->required(array('expires', 'pcode', 'signature'));
        $qs = $params->queryString();
    }
}