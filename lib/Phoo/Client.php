<?php
namespace Phoo;

/**
 * REST Client
 * Makes RESTful HTTP requests on webservices
 *
 * Based on 'Client' library from Alloy Framework
 * @link http://alloyframework.com/
 *
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Client
{
    /**
     * GET
     * 
     * @param string $url URL to perform action on
     * @param optional array $params Array of key => value parameters to pass
     */
    public function get($url, $params = array(), array $options = array())
    {
        return $this->_fetch($url, $params, 'GET', $options);
    }
    
    
    /**
     * POST
     * 
     * @param string $url URL to perform action on
     * @param optional array $params Array of key => value parameters to pass
     */
    public function post($url, $params = array(), array $options = array())
    {
        return $this->_fetch($url, $params, 'POST', $options);
    }
    
    
    /**
     * PUT
     * 
     * @param string $url URL to perform action on
     * @param optional array $params Array of key => value parameters to pass
     */
    public function put($url, $params = array(), array $options = array())
    {
        return $this->_fetch($url, $params, 'PUT', $options);
    }
    
    
    /**
     * DELETE
     * 
     * @param string $url URL to perform action on
     * @param optional array $params Array of key => value parameters to pass
     */
    public function delete($url, $params = array(), array $options = array())
    {
        return $this->_fetch($url, $params, 'DELETE', $options);
    }
    
    
    /**
     * Fetch a URL with given parameters
     *
     * @return \Phoo\Response
     */
    protected function _fetch($url, $params = null, $method = 'GET', array $options = array())
    {
        $method = strtoupper($method);
        
        $urlParts = parse_url($url);
        
        // Build querystring for URL
        if(!is_array($params)) {
            $queryString = (string) $params;
        } else {
            $queryString = $params;
        }
        
        // Append params to URL as query string if not a POST
        if($method != 'POST') {
            $url = $url . "?" . $queryString;
        }
        
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
        
        return new Response($url, $response, $responseInfo);
    }
}