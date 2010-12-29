<?php
namespace Phoo;

/**
 * Ooyala API Response object
 * Contianer that holds response to check HTTP status code, parse XML, etc.
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Response
{
    // Credentials
    protected $_body;
    protected $_status;
    
    
    /**
     * Construct
     */
    public function __construct($body, $status)
    {
        $this->_body = $body;
        $this->_status = $status;
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
     * Output full raw response body
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->_body;
    }
}