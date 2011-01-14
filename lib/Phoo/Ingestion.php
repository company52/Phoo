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
     */
    public function uploadPromoImage($params, $image, $imageSize = 0)
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
            if($imageSize < 1) {
                throw new \InvalidArgumentException("Parameter 3 expected to be filesize since resource handle was given for parameter 2.");
            }
            $opts['fileSize'] = $imageSize;
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
            $opts['fileSize'] = filesize($image);
        }
        
        
        // HTTP stream context
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'request_fulluri' => true,
                'header' => "Content-type: multipart/form-data\r\n" .
                            "Content-Length: " . filesize($image),
                'content' => file_get_contents($image)
            )
        ));
        
        
        /**
         * @link http://www.php.net/manual/en/function.stream-context-create.php#90411
         */
        /*
        $data = ""; 
        $boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10);
        
        // Collect Postdata 
        foreach($postdata as $key => $val) 
        { 
            $data .= "--$boundary\n"; 
            $data .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n"; 
        } 
         
        $data .= "--$boundary\n"; 
        
        // Collect Filedata 
        foreach($files as $key => $file) 
        { 
            $fileContents = file_get_contents($file['tmp_name']); 
            
            $data .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$file['name']}\"\n"; 
            $data .= "Content-Type: image/jpeg\n"; 
            $data .= "Content-Transfer-Encoding: binary\n\n"; 
            $data .= $fileContents."\n"; 
            $data .= "--$boundary--\n"; 
        } 
        
        $url = $this->_apiEndpoints['promoImage'];
        $fp = fopen($url, 'rb', false, $context); 
        if(!$fp) { 
           throw new \Exception("Problem with $url, $php_errormsg"); 
        } 
        
        $response = @stream_get_contents($fp); 
        if($response === false) { 
           throw new \Exception("Problem reading data from $url, $php_errormsg"); 
        } 
        return $response;
        
        var_dump($result);
        exit();
        */
        return $this->client()->post($this->_apiEndpoints['promoImage'], $params->toArray() + array('file' => '@' . $image), $opts);
    }
}