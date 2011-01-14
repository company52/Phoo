<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Test_Analytics extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;
    
    /**
     * 
     */
    public function testVideoTotalsReturnsResponseObject()
    {
        $api = phoo_api('analytics');
        $res = $api->videoTotals(array(
            'expires' => '1893013926',
            'date' => 'month',
            'format' => 'json',
            'granularity' => 'day',
        ), array('ZlYjc3OmkrBX8EsO7SIV5JcAH3J9Oxh3'));
        
        $this->assertInstanceOf('\Phoo\Response', $res);
        return $res;
    }
    
    
    /**
     * Inspect response to ensure it is a valid JSON object
     * 
     * @depends testVideoTotalsReturnsResponseObject
     */
    public function testVideoTotalResponseIsJSON($res)
    {
        $json = $res->parse();
        
        $this->assertInstanceOf('\stdClass', $json);
        return $json;
    }
    
    
    /**
     * Inspect response to ensure it has valid nodes we were expecting
     * 
     * @depends testVideoTotalResponseIsJSON
     */
    public function testVideoTotalResponseContainsItemNodes($json)
    {
        $this->assertEquals(1, count($json->video));
        return $json;
    }
    
    
    /**
     * 
     */
    public function testDomainTotalsReturnsResponseObject()
    {
        $api = phoo_api('analytics');
        $res = $api->domainTotals(array(
            'expires' => '1893013926',
            'date' => 'month',
            'format' => 'json',
            'granularity' => 'day',
            'domain' => 'ooyala.com'
        ));
        
        $this->assertInstanceOf('\Phoo\Response', $res);
        return $res;
    }
    
    
    /**
     * Inspect response to ensure it is a valid JSON object
     * 
     * @depends testDomainTotalsReturnsResponseObject
     */
    public function testDomainTotalResponseIsJSON($res)
    {
        $json = $res->parse();
        
        $this->assertInstanceOf('\stdClass', $json);
        return $json;
    }
    
    
    /**
     * Inspect response to ensure it has valid nodes we were expecting
     * 
     * @depends testDomainTotalResponseIsJSON
     */
    public function testDomainTotalResponseContainsItemNodes($json)
    {
        $this->assertEquals(1, count($json->domain));
        return $json;
    }
}