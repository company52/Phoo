<?php
namespace Phoo;

/**
 * Ooyala Backlot API wrapper
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Backlot
{
    protected $_partnerCode;
    protected $_secretCode;
    
    /**
     * API URL endpoints for supported Backlot API functions
     */
    protected $_apiEndpoints = array(
        'query' => 'http://www.ooyala.com/partner/query',
        'thumbnail' => 'http://api.ooyala.com/partner/thumbnails',
        'attribute' => 'http://api.ooyala.com/partner/edit',
        'metadata' => 'http://api.ooyala.com/partner/set_metadata',
        'labels' => 'http://api.ooyala.com/partner/labels',
        'player' => 'http://api.ooyala.com/partner/players'
    );
    
    
    /**
     * @param string $partnerCode Parter code provided by Oomyala
     */
    public function __construct($partnerCode, $secretCode)
    {
        $this->_partnerCode = $partnerCode;
        $this->_secretCode = $secretCode;
    }
    
    
    /**
     * Get params object and optionally set key => value parameters
     */
    public function params(array $params = array())
    {
        $paramsObj = new Params($this->_partnerCode, $this->_secretCode);
        $paramsObj->set($params);
        return $paramsObj;
    }
    
    
    /**
     * Normalize params to Params object so we can get signature hash and generate URL
     *
     * @return \Phoo\Params
     * @throws \InvalidArgumentException
     */
    public function toParams($params)
    {
        // Params object already
        if($params instanceof Params) {
            return $params;
        }
        
        // Array to Params object
        if(null === $params) {
            $params = array();
        }
        if(is_array($params)) {
            return $this->params($params);
        }
        
        // Bad type
        throw new \InvalidArgumentException("Expected \$params to be array or " . __NAMESPACE__ . "\Params object. Given (" . gettype($params) . ")");
    }
    
    
    /**
     * Query API
     */
    public function query($params)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'signature'));
        return $this->_fetch($this->_apiEndpoints['query'], $params, "GET");
    }
    
    
    /**
     * Thumbnais Query API
     */
    public function thumbnails($params)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'embedCode', 'range', 'resolution', 'signature'));
        return $this->_fetch($this->_apiEndpoints['thumbnail'], $params, "GET");
    }
    
    
    /**
     * Fetch a URL with given parameters
     *
     * @return \Phoo\Response
     */
    protected function _fetch($url, $params = null, $method = 'GET')
    {
        $method = strtoupper($method);
        
        $urlParts = parse_url($url);
        
        // Build querystring for URL
        if(is_array($params)) {
            $queryString = http_build_query($params);
        } else {
            $queryString = (string) $params;
        }
        
        // Append params to URL as query string if not a POST
        if($method != 'POST') {
            $url = $url . "?" . $queryString;
        }
        
        //echo $url;
        //var_dump("Fetching External URL: [" . $method . "] " . $url, $params);
        
        // Use cURL
        if(function_exists('curl_init')) {
            $ch = curl_init($urlParts['host']);
            
            // METHOD differences
            switch($method) {
                case 'GET':
                    curl_setopt($ch, CURLOPT_URL, $url . "?" . $queryString);
                break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
                break;
                 
                case 'PUT':
                    curl_setopt($ch, CURLOPT_URL, $url);
                    $putData = file_put_contents("php://memory", $queryString);
                    curl_setopt($ch, CURLOPT_PUT, true);
                    curl_setopt($ch, CURLOPT_INFILE, $putData);
                    curl_setopt($ch, CURLOPT_INFILESIZE, strlen($queryString));
                break;
                 
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_URL, $url . "?" . $queryString);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            }
            
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the data
            curl_setopt($ch, CURLOPT_HEADER, false); // Get headers
            
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            
            // HTTP digest authentication
            if(isset($urlParts['user']) && isset($urlParts['pass'])) {
                $authHeaders = array("Authorization: Basic ".base64_encode($urlParts['user'].':'.$urlParts['pass']));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeaders);
            }
            
            $response = curl_exec($ch);
            $responseInfo = curl_getinfo($ch);
            curl_close($ch);
            
        // Use streams... (eventually)
        } else {
            throw new Exception(__METHOD__ . " Requres the cURL library to work.");
        }
        
        return new Response($response, $responseInfo);
    }
}