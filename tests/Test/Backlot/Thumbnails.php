<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Test_Backlot_Thumbnails extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;
    
    /**
     * 
     */
    public function testThumbnailsReturnsResponseObject()
    {
        $backlot = phoo_backlot();
        $res = $backlot->thumbnails(array(
            'embedCode' => 'RyY2IxOtfOye1qEPARlzC5S9oPt0tFeH',
            'expires' => '1893013926',
            'range' => '0-25',
            'resolution' => '600x400'
        ));
        
        $this->assertInstanceOf('\Phoo\Response', $res);
        return $res;
    }
    
    
    /**
     * Inspect response to ensure it is a valid XML response
     * 
     * @depends testThumbnailsReturnsResponseObject
     */
    public function testThumbnailsResponseIsXML($res)
    {
        $xml = $res->parse();
        
        $this->assertInstanceOf('\SimpleXMLElement', $xml);
        return $xml;
    }
    
    
    /**
     * Inspect response to ensure it has valid XML nodes we were expecting
     * 
     * @depends testThumbnailsResponseIsXML
     */
    public function testThumbnailsResponseContainsThumbnailNodes($xml)
    {
        $this->assertGreaterThan(0, count($xml->thumbnail), "No <thumbnail> nodes found");
        return $xml;
    }
}