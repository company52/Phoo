<?php
namespace Phoo;

/**
 * Ooyala Backlot API wrapper
 *
 * @package Phoo
 * @link http://github.com/company52/Phoo
 */
class Backlot extends APIWrapper
{
    /**
     * API URL endpoints for supported Backlot API functions
     */
    protected $_apiEndpoints = array(
        'query' => 'http://www.ooyala.com/partner/query',
        'thumbnail' => 'http://api.ooyala.com/partner/thumbnails',
        'attributes' => 'http://api.ooyala.com/partner/edit',
        'metadata' => 'http://api.ooyala.com/partner/set_metadata',
        'labels' => 'http://api.ooyala.com/partner/labels',
        'player' => 'http://api.ooyala.com/partner/players',
        'channel' => 'http://api.ooyala.com/partner/channels'
    );
    
    
    /**
     * Query API
     */
    public function query($params)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'signature'));
        return $this->client()->get($this->_apiEndpoints['query'], $params->queryString());
    }
    
    
    /**
     * Thumbnais Query API
     */
    public function thumbnails($params)
    {
        $params = $this->toParams($params)
            ->required(array('pcode', 'expires', 'embedCode', 'range', 'resolution', 'signature'));
        return $this->client()->get($this->_apiEndpoints['thumbnail'], $params->queryString());
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
        
        return $this->client()->get($this->_apiEndpoints['attributes'], $params->queryString());
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
        if(100 < count($attrs)) {
            throw new \OverflowException("A maximum of 100 name/value pairs can be set per asset. You attempted (" . count($attrs) . ").");
        }
        $params->set($attrs);
        
        // Format delete values according to docs
        if(is_array($params->delete)) {
            $params->delete = implode("\0", $params->delete);
        }
        
        $params->required(array('pcode', 'expires', 'embedCode', 'signature'));
        return $this->client()->get($this->_apiEndpoints['metadata'], $params->queryString());
    }
    
    
    /**
     * List existing labels for asset
     *
     * @param array $params Params used to find asset to update
     * @return \Phoo\Response
     */
    public function listLabels($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'listLabels';
        $params->required(array('expires'));
        return $this->_labelsRequest($params);
    }
    
    
    /**
     * Create labels for asset
     *
     * @param array $params Params used to find asset to update
     * @param array $labels List of labels
     * @return \Phoo\Response
     */
    public function createLabels($params, array $labels)
    {
        $params = $this->toParams($params);
        $params->set(array(
            'mode' => 'createLabels',
            'label' => $labels
        ));
        return $this->_labelsRequest($params);
    }
    
    
    /**
     * Delete labels for asset
     *
     * @param array $params Params used to find asset to update
     * @param array $labels List of labels
     * @return \Phoo\Response
     */
    public function deleteLabels($params, array $labels)
    {
        $params = $this->toParams($params);
        $params->set(array(
            'mode' => 'deleteLabels',
            'label' => $labels
        ));
        return $this->_labelsRequest($params);
    }
    
    
    /**
     * Assign labels for asset
     *
     * @param array $params Params used to find asset to update
     * @param array $labels List of labels
     * @return \Phoo\Response
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
    
    
    /**
     * Unassign labels for asset
     *
     * @param array $params Params used to find asset to update
     * @param array $labels List of labels
     * @return \Phoo\Response
     */
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
    
    
    /**
     * Rename label across multiple assets
     *
     * @param array $params Params used to find asset to update
     * @return \Phoo\Response
     */
    public function renameLabel($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'renameLabels';
        $params->required(array('embedCodes', 'oldlabel', 'newlabel'));
        
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
    
    
    /**
     * Clear all labels from one or more assets
     *
     * @param array $params Params used to find asset to update
     * @return \Phoo\Response
     */
    public function clearLabels($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'clearLabels';
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
    
    
    /**
     * Perform an HTTP request to the Labels API endpoint
     *
     * @return \Phoo\Response
     */
    protected function _labelsRequest(Params $params)
    {
        $params->required(array('expires', 'mode'));
        return $this->client()->get($this->_apiEndpoints['labels'], $params->queryString());
    }
    
    
    /**
     * Clear all labels from one or more assets
     *
     * @param array $params Params used to find asset to update
     * @return \Phoo\Response
     */
    public function listPlayers($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'list';
        $params->required(array('expires', 'mode'));
        
        // If user set 'embedCode' like other API calls, go ahead and take care of it for them (convert to plural as needed by this specific API call).
        if($params->embedCode && !$params->embedCodes) {
            $params->embedCodes = (array) $params->embedCode;
            unset($params->embedCode);
        }
        
        // Make sure embedCodes are comma-separated if given as an array, per API docs
        if(is_array($params->embedCodes)) {
            $params->embedCodes = implode(',', $params->embedCodes);
        }
        
        return $this->_playerRequest($params);
    }
    
    
    /**
     * Assigns a comma-separated list of video embed codes (or a single embed code) to a particular player.
     * An existing player will be overwritten when using this mode.
     *
     * @param array $params Params used to find asset to update
     * @return \Phoo\Response
     */
    public function assignPlayers($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'assign';
        $params->required(array('expires', 'mode', 'embedCodes', 'pid'));
        
        // If user set 'embedCode' like other API calls, go ahead and take care of it for them (convert to plural as needed by this specific API call).
        if($params->embedCode && !$params->embedCodes) {
            $params->embedCodes = (array) $params->embedCode;
            unset($params->embedCode);
        }
        
        // Make sure embedCodes are comma-separated if given as an array, per API docs
        if(is_array($params->embedCodes)) {
            $params->embedCodes = implode(',', $params->embedCodes);
        }
        
        return $this->_playerRequest($params);
    }
    
    
    /**
     * Perform an HTTP request to the Labels API endpoint
     *
     * @return \Phoo\Response
     */
    protected function _playerRequest(Params $params)
    {
        $params->required(array('expires', 'mode'));
        return $this->client()->get($this->_apiEndpoints['player'], $params->queryString());
    }
    
    
    /**
     * Creates new channel
     *
     * @param array $params Params used for request
     * @param string String title of channel
     * @return \Phoo\Response
     */
    public function createChannel($params, $title)
    {
        $params = $this->toParams($params);
        $params->mode = 'create';
        $params->title = $title;
        $params->required(array('expires', 'mode', 'title'));
        return $this->_channelRequest($params);
    }
    
    
    /**
     * Lists all channel components
     *
     * @param array $params Params used for request
     * @return \Phoo\Response
     */
    public function listChannels($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'list';
        $params->required(array('expires', 'mode', 'channelEmbedCode'));
        return $this->_channelRequest($params);
    }
    
    
    /**
     * Assigns a comma-separated list of video embed codes (or a single embed code) to a particular player.
     * An existing player will be overwritten when using this mode.
     *
     * @param array $params Params used to find asset to update
     * @return \Phoo\Response
     */
    public function assignChannels($params)
    {
        $params = $this->toParams($params);
        $params->mode = 'assign';
        $params->required(array('expires', 'mode', 'channelEmbedCode', 'embedCodes'));
        
        // If user set 'embedCode' like other API calls, go ahead and take care of it for them (convert to plural as needed by this specific API call).
        if($params->embedCode && !$params->embedCodes) {
            $params->embedCodes = (array) $params->embedCode;
            unset($params->embedCode);
        }
        
        // Make sure embedCodes are comma-separated if given as an array, per API docs
        if(is_array($params->embedCodes)) {
            $params->embedCodes = implode(',', $params->embedCodes);
        }
        
        return $this->_channelRequest($params);
    }
    
    
    /**
     * Create dynamic channel
     *
     * @see http://www.ooyala.com/support/docs/backlot_api#dynamic_channel
     * @param array $params Params used for request
     * @param string String title of channel
     * @return \Phoo\Response
     */
    public function createDynamicChannel($params)
    {
        $params = $this->toParams($params);
        $params->required(array('expires', 'mode', 'title'));
        
        // Set default params to send
        $params->set(array(
            'mode' => 'create',
            'dynamicChannel' => 'true'
        ))
        ->defaults(array(
            'labels' => '*'
        ));
        
        // Make sure labels are comma-separated if given as an array, per API docs
        if(is_array($params->labels)) {
            $params->labels = implode(',', $params->labels);
        }
        
        return $this->_channelRequest($params);
    }
    
    
    /**
     * Perform an HTTP request to the Labels API endpoint
     *
     * @return \Phoo\Response
     */
    protected function _channelRequest(Params $params)
    {
        $params->required(array('expires', 'mode'));
        return $this->client()->get($this->_apiEndpoints['channel'], $params->queryString());
    }
}