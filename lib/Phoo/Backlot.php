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
    /**
     * @param string $partnerCode Parter code provided by Oomyala
     */
    public function __construct($partnerCode, $secretCode)
    {
        $this->_partnerCode = $partnerCode;
        $this->_secretCode = $secretCode;
    }
    
    
    /**
     * Get signature 
     */
    public function signature()
    {
        return "dDiJo3LKLqPnqCpzEHDYBBNBe%2FmBgV3%2BVt9eiTgFYGk";
    }
}