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
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     MIT http://opensource.org/licenses/MIT
 */

/**
 * Emails Context
 */
class Thrive_Dash_Api_Mautic_Emails extends Thrive_Dash_Api_Mautic_Api {

	/**
	 * {@inheritdoc}
	 */
	protected $endpoint = 'emails';

	/**
	 * {@inheritdoc}
	 */
	public function create( array $parameters ) {
		return $this->actionNotSupported( 'create' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function edit( $id, array $parameters, $createIfNotExists = false ) {
		return $this->actionNotSupported( 'edit' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $id ) {
		return $this->actionNotSupported( 'delete' );
	}

	/**
	 * Send email to the assigned lists
	 *
	 * @param int $id
	 *
	 * @return array|mixed
	 */
	public function send( $id ) {
		return $this->makeRequest( $this->endpoint . '/' . $id . '/send', array(), 'POST' );
	}

	/**
	 * Send email to a specific contact
	 *
	 * @param int $id
	 * @param int $contactId
	 *
	 * @return array|mixed
	 */
	public function sendToContact( $id, $contactId ) {
		return $this->makeRequest( $this->endpoint . '/' . $id . '/send/contact/' . $contactId, array(), 'POST' );
	}

	/**
	 * Send email to a specific lead
	 *
	 * @deprecated use sendToContact instead
	 *
	 * @param int $id
	 * @param int $leadId
	 *
	 * @return array|mixed
	 */
	public function sendToLead( $id, $leadId ) {
		return $this->sendToContact( $id, $leadId );
	}
}
