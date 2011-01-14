<?php
/**
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Test_Ingestion extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;
    
    /**
     * 
     */
    public function testUploadPromoImageReturnsResponseObject()
    {
        // Test upload an image file
        $imageFile = dirname(__DIR__) . '/testImageUpload.jpg';
        
        $api = phoo_api('ingestion');
        
        $res = $api->uploadPromoImage(array(
            'expires' => '1893013926',
            'embedCode' => 'ZlYjc3OmkrBX8EsO7SIV5JcAH3J9Oxh3'
        ), $imageFile);
        
        $this->assertInstanceOf('\Phoo\Response', $res);
        return $res;
    }
    
    
    /**
     * Inspect response to ensure it is a valid XML object
     * 
     * @depends testUploadPromoImageReturnsResponseObject
     */
    public function testUploadPromoImageResponseIsXML($res)
    {
        try {
            $xml = $res->parse();
        } catch(\Exception $e) {
            echo "\n\n";
            echo $e->getMessage();
            echo "\n";
            echo $res;
            echo "\n\n";
        }
        $this->assertInstanceOf('\SimpleXMLElement', $xml);
        return $xml;
    }
    
    
    /**
     * Inspect response to ensure it has valid nodes we were expecting
     * 
     * @depends testUploadPromoImageResponseIsXML
     */
    public function testUploadPromoImageResponseContainsUploadedFileNode($xml)
    {
        $this->assertEquals(1, count($xml->uploadedFile));
        return $xml;
    }
}