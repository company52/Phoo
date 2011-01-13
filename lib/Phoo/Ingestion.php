<?php
namespace Phoo;

/**
 * Ooyala Ingestion API wrapper
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Ingestion extends APIWrapper
{
    /**
     * API URL endpoints for supported Backlot API functions
     */
    protected $_apiEndpoints = array(
        'promoImage' => 'http://uploader.ooyala.com/api/upload/preview'
    );
    
    
    /**
     * Query API
     */
    public function promoImageUpload($params, $image)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'embed_code', 'signature'));
            
        // The 'embed_code' parameter does not match format of Backlot APIs where it is 'embedCode'. Let's help users out with that...
        if($params->embedCode && !$params->embed_code) {
            $params->embed_code = $params->embedCode;
            unset($params->embedCode);
        }
        
        // Handle file upload
        $opts = array();
        if(is_resource($image)) {
            $opts['fileHandle'] = $image;
        } else {
            $fh = false;
            if(is_string($image)) {
                $fh = @fopen($image, 'r');
            }
            
            // Bad file
            if(!$fh) {
                throw new \InvalidArgumentException("Parameter 2 expected to be local file or resource handle. Given (" . gettype($image) . ")");
            }
            
            $opts['fileHandle'] = $fh;
        }
        
        return $this->client()->post($this->_apiEndpoints['promoImage'], $params->queryString(), $opts);
    }
}