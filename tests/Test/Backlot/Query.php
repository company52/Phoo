<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Test_Backlot_Query extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;
    
    /**
     * 
     */
    public function testQueryReturnsResponseObject()
    {
        $backlot = phoo_backlot();
        $res = $backlot->query(array(
            'expires' => '1893013926',
            'label' => array('any/some'),
            'statistics' => '1d,2d,7d,28d,30d,31d,lifetime,',
            'status' => 'upl,live,',
            'title' => 'a'
        ));
        
        $this->assertInstanceOf('\Phoo\Response', $res);
        return $res;
    }
    
    
    /**
     * Inspect response to ensure it is a valid XML response
     * 
     * @depends testQueryReturnsResponseObject
     */
    public function testQueryResponseIsXML($res)
    {
        $xml = $res->parse();
        $this->assertInstanceOf('\SimpleXMLElement', $xml);
        return $xml;
    }
    
    
    /**
     * Inspect response to ensure it has valid XML nodes we were expecting
     * 
     * @depends testQueryResponseIsXML
     */
    public function testQueryResponseContainsItemNodes($xml)
    {
        $this->assertGreaterThan(0, count($xml->item), "No <item> nodes found");
        return $xml;
    }
}