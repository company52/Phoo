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
     * @param string $partnerCode Parter code provided by Oomyala
     */
    public function __construct($partnerCode, $secretCode)
    {
        $this->_partnerCode = $partnerCode;
        $this->_secretCode = $secretCode;
    }
    
    
    /**
     * Get query object
     */
    public function query(array $params = array())
    {
        $query = new Query($this->_partnerCode, $this->_secretCode);
        $query->params($params);
        return $query;
    }
}