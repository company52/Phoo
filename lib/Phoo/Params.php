<?php
namespace Phoo;

/**
 * Ooyala Params object
 * Used to set key/value params to send with request and generate corresponding API Signature
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Params
{
    // Credentials
    protected $_partnerCode;
    protected $_secretCode;
    
    // Query params
    protected $_params = array();
    protected $_paramsRequired = array();
    
    
    /**
     * @param string $partnerCode Parter code provided by Oomyala
     */
    public function __construct($partnerCode, $secretCode)
    {
        $this->_partnerCode = $partnerCode;
        $this->_secretCode = $secretCode;
    }
    
    
    /**
     * Set query parameters to send with request
     */
    public function set(array $params = array())
    {
        $this->_params = array_merge($this->_params, $params);
        return $this;
    }
    
    
    /**
     * Get param that has been set
     */
    public function __get($key)
    {
        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }
    
    
    /**
     * Set param
     */
    public function __set($key, $value)
    {
        $this->_params[$key] = $value;
    }
    
    
    /**
     * Isset param?
     */
    public function __isset($key)
    {
        return isset($this->_params[$key]);
    }
    
    
    /**
     * Unset param
     */
    public function __unset($key)
    {
        unset($this->_params[$key]);
    }
    
    
    /**
     * Set default parameter values to send with request if not already specified
     */
    public function defaults(array $defaults)
    {
        // The plus operator on arrays only fills-in keys from the second array that are not already set in the first
        $this->_params = $this->_params + $defaults;
        return $this;
    }
    
    
    /**
     * Set/get required parameters
     */
    public function required(array $params = array())
    {
        if(0 == count($params)) {
            return $this->_paramsRequired;
        }
        $this->_paramsRequired = array_unique($this->_paramsRequired + array_flip($params));
        return $this;
    }
    
    
    /**
     * Check params to ensure required ones are set
     *
     * @throws Exception
     */
    public function checkRequiredParams()
    {
        // Remove signature from requirements because it is automatically added when building the query string
        if(isset($this->_paramsRequired['signature'])) {
            unset($this->_paramsRequired['signature']);
        }
        
        // Remove pcode from requirements because it is automatically prepended when building the query string
        if(isset($this->_paramsRequired['pcode'])) {
            unset($this->_paramsRequired['pcode']);
        }
        
        $missing = array_keys(array_diff_key($this->_paramsRequired, $this->_params));
        if(count($missing) > 0) {
            throw new \UnexpectedValueException("Required params missing (" . var_export($missing, true) . ")");
        }
    }
    
    
    /**
     * Get signature hash for current query
     *
     * @link http://www.ooyala.com/support/docs/backlot_api#signing
     */
    public function signature()
    {
        // 1. Begin with the 40 character Secret Code from the Developers area of your Backlot Account tab.
        $str = $this->_secretCode;
        
        // 2. Sort the parameter names alphabetically and append <name>=<value> pairs to the string
        ksort($this->_params);
        $str .= urldecode(http_build_query($this->_params, null, ''));
        
        // 3. Generate an SHA-256 digest in base 64 format on this string
        //    - truncate the string to 43 characters and
        //    - drop any trailing '=' signs
        //    - URI encode the signature specifically '+','=', and '/'
        $str = rawurlencode(trim(substr(base64_encode(hash('sha256', $str, true)), 0, 43), '='));
        return $str;
    }
    
    
    /**
     * Get query string from given params
     *
     * @return string
     * @throws \UnexpectedValueException When required parameters are not set or given
     */
    public function queryString()
    {
        $sig = $this->signature();
        $this->checkRequiredParams();
        $params = array('pcode' => $this->_partnerCode) + $this->_params;
        $str = urldecode(http_build_query($params, null, '&'));
        return $str . "&signature=" . $sig;
    }
    
    
    /**
     * Get full parameter set as array of key => value pairs
     *
     * @return array
     * @throws \UnexpectedValueException When required parameters are not set or given
     */
    public function toArray()
    {
        $sig = $this->signature();
        $this->checkRequiredParams();
        $params = array_merge(array('pcode' => $this->_partnerCode), $this->_params, array('signature' => $sig));
        return $params;
    }
    
    
    /**
     * Output query string to append to URL endpoint with signature and partner code
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->queryString();
        } catch(\Exception $e) {
            return $e->getTraceAsString();
        }
    }
}