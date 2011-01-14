<?php
namespace Phoo;

/**
 * Ooyala Analytics API wrapper
 * 
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Analytics extends APIWrapper
{
    /**
     * API URL endpoints for supported Backlot API functions
     */
    protected $_apiEndpoints = array(
        'analytics' => 'http://api.ooyala.com/api/analytics'
    );
    
    
    /**
     * Get video totals
     *
     * @param array $params Params used to find asset to update
     * @param array $videos List of video embed codes to get stats for
     */
    public function videoTotals($params, array $videos)
    {
        $params = $this->toParams($params)
            ->defaults(array(
                'date' => 'month',
                'format' => 'json',
                'granularity' => 'day'
            ))
            ->required(array('pcode', 'date', 'expires', 'format', 'granularity', 'method', 'signature'));
        
        // Method
        $params->method = 'video.totals';
        
        // Make sure video embedCodes are comma-separated if given as an array, per API docs
        $params->video = implode(',', $videos);
        
        return $this->client()->post($this->_apiEndpoints['analytics'], $params->queryString());
    }
    
    
    /**
     * Get domain totals
     * Optionally limit to certain video embed codes
     *
     * @param array $params Params used to find asset to update
     * @param array $videos List of video embed codes to get stats for (optional limitation)
     */
    public function domainTotals($params, array $videos = array())
    {
        $params = $this->toParams($params)
            ->defaults(array(
                'date' => 'month',
                'format' => 'json',
                'granularity' => 'day'
            ))
            ->required(array('pcode', 'date', 'expires', 'format', 'granularity', 'method', 'domain', 'signature'));
        
        // Method
        $params->method = 'domain.totals';
        
        // Make sure video embedCodes are comma-separated if given as an array, per API docs
        if(count($videos) > 0) {
            $params->video = implode(',', $videos);
        }
        
        return $this->client()->post($this->_apiEndpoints['analytics'], $params->queryString());
    }
}