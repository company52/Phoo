<?php
namespace Phoo;

/**
 * Ooyala API Response object
 * Contianer that holds response to check HTTP status code, parse XML and JSON responses, etc.
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Response
{
    protected $_url;
    protected $_body;
    protected $_info;
    protected $_status;
    
    
    /**
     * Construct
     */
    public function __construct($url, $body, $info)
    {
        $this->_url = $url;
        $this->_body = $body;
        $this->_info = $info;
        $this->_status = $info['http_code'];
    }
    
    
    /**
     * Get/set status code
     */
    public function status($code = null)
    {
        if(null === $code) {
            return $this->_status;
        }
        
        $this->_status = $code;
        return $this;
    }
    
    
    /**
     * Get/set response body
     */
    public function body($body = null)
    {
        if(null === $body) {
            return $this->_body;
        }
        
        $this->_body = $body;
        return $this;
    }
    
    
    /**
     * Detect whether or not response was an error
     * 
     * @return boolean
     */
    public function isError()
    {
        return $this->_status >= 400;
    }
    
    
    /**
     * Convert response body into native PHP objects from JSON response
     * 
     * @return stdClass
     */
    public function fromJson()
    {
        return json_decode($this->_body);
    }
    
    
    /**
     * Convert response body into SimpleXMLElement DOM nodes
     *
     * @return SimpleXMLElement
     * @link http://us.php.net/manual/en/class.simplexmlelement.php
     */
    public function fromXml()
    {
        return simplexml_load_string($this->_body);
    }
    
    
    /**
     * Parse response body based in information received from HTTP response about its contents
     * Currently supports XML and JSON responses
     *
     * @return \SimpleXMLElement or \stdClass
     * @throws \UnexpectedValueException
     */
    public function parse()
    {
        $status = $this->status();
        if($status >= 400) {
            switch($status) {
                // 4xx
                case 400:
                    throw new Exception\Http\BadRequest("");
                break;
                case 401:
                    throw new Exception\Http\Unauthorized("");
                break;
                case 403:
                    throw new Exception\Http\Forbidden("");
                break;
                case 404:
                    throw new Exception\Http\NotFound("");
                break;
                case 405:
                    throw new Exception\Http\MethodNotAllowed("");
                break;
                case 406:
                    throw new Exception\Http\NotAccecptable("");
                break;
                // 5xx
                case 500:
                    throw new Exception\Http\InternalServerError("");
                break;
                case 502:
                    throw new Exception\Http\BadGateway("");
                break;
                case 503:
                    throw new Exception\Http\ServiceUnavailable("");
                break;
                default:
                    throw new Exception\Http("HTTP " . $status . " Error Returned", $status);
                break;
            }
        }
        
        // XML response, acconting for inforrect Content-Type in the response headers
        if(false !== strpos($this->_info['content_type'], 'xml')
           || ($this->_info['content_type'] == 'text/html' && false !== strpos($this->_body, '<?xml '))) {
            return $this->fromXml();
        }
        
        if(false !== strpos($this->_info['content_type'], 'json')) {
            return $this->fromJson();
        }
        
        throw new \UnexpectedValueException("Response type expected was XML or JSON. Received: (" . $this->_info['content_type'] . ")");
    }
    
    
    /**
     * Output full raw response body
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->_body;
    }
}