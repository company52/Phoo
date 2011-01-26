<?php
namespace Phoo;

/**
 * Ooyala Player object
 * Used to set key/value params to generate HTML codes/tags required
 *
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Player
{
    protected $_params = array();
    
    
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
     * Get query string from given params
     *
     * @return string
     * @throws \UnexpectedValueException When required parameters are not set or given
     */
    public function queryString()
    {
        $str = http_build_query($this->_params, null, '&');
        return $str;
    }
    
    
    /**
     * Return HTML <script> tag with video embed JS
     *
     * @ return string
     */
    public function asEmbedScript()
    {
        return '<script src="http://player.ooyala.com/player.js?' . $this->queryString() . '"></script>';
    }
}