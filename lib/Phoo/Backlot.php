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
     * API URL endpoints for supported Backlot API functions
     */
    protected $_apiEndpoints = array(
        'query' => 'http://www.ooyala.com/partner/query',
        'thumbnail' => 'http://api.ooyala.com/partner/thumbnails',
        'attributes' => 'http://api.ooyala.com/partner/edit',
        'metadata' => 'http://api.ooyala.com/partner/set_metadata',
        'labels' => 'http://api.ooyala.com/partner/labels',
        'player' => 'http://api.ooyala.com/partner/players'
    );
    
    
    /**
     * @param string $partnerCode Parter code provided by Oomyala
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
     * Query API
     */
    public function query($params)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'signature'));
        return $this->_fetch($this->_apiEndpoints['query'], $params->queryString(), "GET");
    }
    
    
    /**
     * Thumbnais Query API
     */
    public function thumbnails($params)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'embedCode', 'range', 'resolution', 'signature'));
        return $this->_fetch($this->_apiEndpoints['thumbnail'], $params->queryString(), "GET");
    }
    
    
    /**
     * Attribute Update API
     * 
     * @param array $params Params used to find asset to update
     * @param array $attrs Key=value attributes to set on selected asset
     * @return \Phoo\Response
     * @throws \UnexpectedValueException
     */
    public function setAttributes($params, array $attrs = array())
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'embedCode', 'signature'))
            ->set($attrs);
        
        // Options are live, paused, or deleted
        if(null !== $params->status && !in_array(strtolower($params->status), array("live", "paused", "deleted"))) {
            throw new \UnexpectedValueException("Status can only be one of 'live', 'paused', or 'deleted'. Given (" . $params->status . ")");
        }
        
        return $this->_fetch($this->_apiEndpoints['attributes'], $params->queryString(), "GET");
    }
    
    
    /**
     * Metadata API
     *
     * @param array $params Params used to find asset to update
     * @param array $attrs Key=value attributes to set on selected asset
     * @return \Phoo\Response
     * @throws \OverflowException
     */
    public function setMetadata($params, array $attrs = array())
    {
        $params = $this->toParams($params);
        
        // From API docs: A maximum of 100 name/value pairs can be set per asset.
        if(100 > count($attrs)) {
            throw new \OverflowException("A maximum of 100 name/value pairs can be set per asset. You attempted (" . count($attrs) . ").");
        }
        $params->set($attrs);
        
        // Format delete values according to docs
        if(is_array($params->delete)) {
            $params->delete = implode("\0", $params->delete);
        }
        
        $params->required(array('pcode', 'expires', 'embedCode', 'signature'));
        return $this->_fetch($this->_apiEndpoints['metadata'], $params->queryString(), "GET");
    }
    
    
    /**
     * List existing labels for asset
     */
    public function listLabels($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'listLabels';
        $params->required(array('embedCode'));
        return $this->_labelsRequest($params);
    }
    
    
    /**
     * Create labels for asset
     */
    public function createLabels($params, array $labels)
    {
        $params = $this->toParams($params);
        $params->set(array(
            'mode' => 'createLabels',
            'label' => $labels
        ));
        $params->required(array('embedCode'));
        return $this->_labelsRequest($params);
    }
    
    
    /**
     * Delete labels for asset
     */
    public function deleteLabels($params, array $labels)
    {
        $params = $this->toParams($params);
        $params->set(array(
            'mode' => 'deleteLabels',
            'label' => $labels
        ));
        $params->required(array('embedCode'));
        return $this->_labelsRequest($params);
    }
    
    
    /**
     * Assign labels for asset
     */
    public function assignLabels($params, array $labels)
    {
        $params = $this->toParams($params);
        $params->set(array(
            'mode' => 'assignLabels',
            'label' => $labels
            ));
        $params->required(array('embedCodes'));
        
        // If user set 'embedCode' like other API calls, go ahead and take care of it for them (convert to plural as needed by this specific API call).
        if($params->embedCode && !$params->embedCodes) {
            $params->embedCodes = (array) $params->embedCode;
            unset($params->embedCode);
        }
        
        // Make sure embedCodes are comma-separated if given as an array, per API docs
        if(is_array($params->embedCodes)) {
            $params->embedCodes = implode(',', $params->embedCodes);
        }
        
        return $this->_labelsRequest($params);
    }
    
    public function unassignLabels($params, array $labels)
    {
        $params = $this->toParams($params);
        $params->set(array(
            'mode' => 'unassignLabels',
            'label' => $labels
            ));
        $params->required(array('embedCodes'));
        
        // If user set 'embedCode' like other API calls, go ahead and take care of it for them (convert to plural as needed by this specific API call).
        if($params->embedCode && !$params->embedCodes) {
            $params->embedCodes = (array) $params->embedCode;
            unset($params->embedCode);
        }
        
        // Make sure embedCodes are comma-separated if given as an array, per API docs
        if(is_array($params->embedCodes)) {
            $params->embedCodes = implode(',', $params->embedCodes);
        }
        
        return $this->_labelsRequest($params);
    }
    
    
    public function renameLabel() {}
    public function clearLabels() {}
    
    
    /**
     * Perform an HTTP request to the Labels API endpoint
     *
     * @return \Phoo\Response
     */
    protected function _labelsRequest(Params $params)
    {
        $params->required(array('expires', 'mode'));
        return $this->_fetch($this->_apiEndpoints['labels'], $params->queryString(), "GET");
    }
    
    
    /**
     * Fetch a URL with given parameters
     *
     * @return \Phoo\Response
     */
    protected function _fetch($url, $params = null, $method = 'GET')
    {
        $method = strtoupper($method);
        
        $urlParts = parse_url($url);
        
        // Build querystring for URL
        if(is_array($params)) {
            $queryString = http_build_query($params);
        } else {
            $queryString = (string) $params;
        }
        
        // Append params to URL as query string if not a POST
        if($method != 'POST') {
            $url = $url . "?" . $queryString;
        }
        
        //echo $url;
        //var_dump("Fetching External URL: [" . $method . "] " . $url, $params);
        
        // Use cURL
        if(function_exists('curl_init')) {
            $ch = curl_init($urlParts['host']);
            
            // METHOD differences
            switch($method) {
                case 'GET':
                    curl_setopt($ch, CURLOPT_URL, $url . "?" . $queryString);
                break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
                break;
                 
                case 'PUT':
                    curl_setopt($ch, CURLOPT_URL, $url);
                    $putData = file_put_contents("php://memory", $queryString);
                    curl_setopt($ch, CURLOPT_PUT, true);
                    curl_setopt($ch, CURLOPT_INFILE, $putData);
                    curl_setopt($ch, CURLOPT_INFILESIZE, strlen($queryString));
                break;
                 
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_URL, $url . "?" . $queryString);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            }
            
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the data
            curl_setopt($ch, CURLOPT_HEADER, false); // Get headers
            
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            
            // HTTP digest authentication
            if(isset($urlParts['user']) && isset($urlParts['pass'])) {
                $authHeaders = array("Authorization: Basic ".base64_encode($urlParts['user'].':'.$urlParts['pass']));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeaders);
            }
            
            $response = curl_exec($ch);
            $responseInfo = curl_getinfo($ch);
            curl_close($ch);
            
        // Use streams... (eventually)
        } else {
            throw new Exception(__METHOD__ . " Requres the cURL library to work.");
        }
        
        return new Response($url, $response, $responseInfo);
    }
}