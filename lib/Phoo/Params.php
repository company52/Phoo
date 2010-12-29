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
    }
    
    
    /**
     * Set/get required parameters
     */
    public function required(array $params = array())
    {
        if(count($params) == 0) {
            return $this->_paramsRequired;
        }
        $this->_paramsRequired = array_merge($this->_paramsRequired, $params);
    }
    
    
    /**
     * Check params to ensure required ones are set
     *
     * @throws Exception
     */
    public function checkRequiredParams()
    {
        $p &= $this->_paramsRequired;
        
        // Remove signature from requirements because it is automatically added when building the query string
        if(isset($p['signature'])) {
            unset($p['signature']);
        }
        
        $missing = array_diff_key($p, $this->_params);
        if(count($missing) > 0) {
            throw new \InvalidArgumentException("Required params missing (" . var_export($missing, true) . ")");
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
     * Output query string to append to URL endpoint with signature and partner code
     *
     * @return string
     */
    public function __toString()
    {
        $sig = $this->signature();
        $params = array('pcode' => $this->_partnerCode) + $this->_params;
        $str = urldecode(http_build_query($params, null, '&'));
        return $str . "&signature=" . $sig;
    }
}