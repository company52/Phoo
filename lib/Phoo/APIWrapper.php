<?php
namespace Phoo;

/**
 * Ooyala Backlot API wrapper
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
abstract class APIWrapper
{
    protected $_partnerCode;
    protected $_secretCode;
    protected $_client;
    
    
    /**
     * @param string $partnerCode Parter code provided by Oomyala
     * @param string $secretCode Secret code provided by Oomyala
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
     * HTTP client to perform HTTP requests
     *
     * @return \Phoo\Client
     */
    public function client()
    {
        if(null === $this->_client) {
            $this->_client = new Client();
        }
        return $this->_client;
    }
}