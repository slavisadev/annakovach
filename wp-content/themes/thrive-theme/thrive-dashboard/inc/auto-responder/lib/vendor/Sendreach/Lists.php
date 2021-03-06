<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
/**
 * This file contains the lists endpoint for MailWizzApi PHP-SDK.
 * 
 * @author Serban George Cristian <cristian.serban@mailwizz.com>
 * @link http://www.mailwizz.com/
 * @copyright 2013-2015 http://www.mailwizz.com/
 */
 
 
/**
 * MailWizzApi_Endpoint_Lists handles all the API calls for lists.
 * 
 * @author Serban George Cristian <cristian.serban@mailwizz.com>
 * @package MailWizzApi
 * @subpackage Endpoint
 * @since 1.0
 */
class Thrive_Dash_Api_Sendreach_Lists extends Thrive_Dash_Api_Sendreach
{
    /**
     * Get all the mail list of the current customer
     * 
     * Note, the results returned by this endpoint can be cached.
     * 
     * @param integer $page
     * @param integer $perPage
     * @return Thrive_Dash_Api_Sendreach_Response
     */
    public function getLists($page = 1, $perPage = 10)
    {
        $client = new Thrive_Dash_Api_Sendreach_Client(array(
            'method'        => Thrive_Dash_Api_Sendreach_Client::METHOD_GET,
            'url'           => $this->config->getApiUrl('lists'),
            'paramsGet'     => array(
                'page'      => (int)$page, 
                'per_page'  => (int)$perPage
            ),
            'enableCache'   => true,
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Get one list
     * 
     * Note, the results returned by this endpoint can be cached.
     * 
     * @param string $listUid
     * @return Thrive_Dash_Api_Sendreach_Response
     */
    public function getList($listUid)
    {
        $client = new Thrive_Dash_Api_Sendreach_Client(array(
            'method'        => Thrive_Dash_Api_Sendreach_Client::METHOD_GET,
            'url'           => $this->config->getApiUrl(sprintf('lists/%s', (string)$listUid)),
            'paramsGet'     => array(),
            'enableCache'   => true,
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Create a new mail list for the customer
     * 
     * The $data param must contain following indexed arrays:
     * -> general
     * -> defaults
     * -> notifications
     * -> company
     * 
     * @param array $data
     * @return Thrive_Dash_Api_Sendreach_Response
     */
    public function create(array $data)
    {
        $client = new Thrive_Dash_Api_Sendreach_Client(array(
            'method'        => Thrive_Dash_Api_Sendreach_Client::METHOD_POST,
            'url'           => $this->config->getApiUrl('lists'),
            'paramsPost'    => $data,
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Update existing mail list for the customer
     * 
     * The $data param must contain following indexed arrays:
     * -> general
     * -> defaults
     * -> notifications
     * -> company
     * 
     * @param string $listUid
     * @param array $data
     * @return Thrive_Dash_Api_Sendreach_Response
     */
    public function update($listUid, array $data)
    {
        $client = new Thrive_Dash_Api_Sendreach_Client(array(
            'method'        => Thrive_Dash_Api_Sendreach_Client::METHOD_PUT,
            'url'           => $this->config->getApiUrl(sprintf('lists/%s', $listUid)),
            'paramsPut'     => $data,
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Copy existing mail list for the customer
     * 
     * @param string $listUid
     * @return Thrive_Dash_Api_Sendreach_Response
     */
    public function copy($listUid)
    {
        $client = new Thrive_Dash_Api_Sendreach_Client(array(
            'method'    => Thrive_Dash_Api_Sendreach_Client::METHOD_POST,
            'url'       => $this->config->getApiUrl(sprintf('lists/%s/copy', $listUid)),
        ));
        
        return $response = $client->request();
    }
    
    /**
     * Delete existing mail list for the customer
     * 
     * @param string $listUid
     * @return Thrive_Dash_Api_Sendreach_Response
     */
    public function delete($listUid)
    {
        $client = new Thrive_Dash_Api_Sendreach_Client(array(
            'method'    => Thrive_Dash_Api_Sendreach_Client::METHOD_DELETE,
            'url'       => $this->config->getApiUrl(sprintf('lists/%s', $listUid)),
        ));
        
        return $response = $client->request();
    }
}