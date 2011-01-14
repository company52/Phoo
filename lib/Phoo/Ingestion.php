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
     * Upload promo image for given video
     *
     * @param array $params Params used to find asset to update
     * @param string $image Full absolute path to local image file that will be uploaded
     * @return \Phoo\Response
     */
    public function uploadPromoImage($params, $image)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'embed_code', 'signature'));
            
        // The 'embed_code' parameter does not match format of Backlot APIs where it is 'embedCode'. Let's help users out with that...
        if($params->embedCode && !$params->embed_code) {
            $params->embed_code = $params->embedCode;
            unset($params->embedCode);
        }
        
        // Handle file upload
        if(!is_string($image)) {
            throw new \InvalidArgumentException("Image (2nd parameter) expected to be string. Given (" . gettype($image) . ").");
        }
        
        return $this->client()->post($this->_apiEndpoints['promoImage'] . '?' . $params->queryString(), array('file' => '@' . $image));
    }
}