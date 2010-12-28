<?php
namespace Phoo;

/**
 * Ooyala Backlot API wrapper
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Query
{
    // Credentials
    protected $_partnerCode;
    protected $_secretCode;
    
    // Query params
    protected $_params = array();
    
    
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
    public function params(array $params = array())
    {
        $this->_params = $this->_params + $params;
    }
    
    
    /**
     * Get signature hash for current query
     *
     * @link 
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
}