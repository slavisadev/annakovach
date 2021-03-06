<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

class Thrive_Dash_List_Connection_MailRelay extends Thrive_Dash_List_Connection_Abstract {
	/**
	 * Return the connection type
	 *
	 * @return String
	 */
	public static function getType() {
		return 'autoresponder';
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return 'MailRelay';
	}

	/**
	 * output the setup form html
	 *
	 * @return void
	 */
	public function outputSetupForm() {
		$related_api = Thrive_Dash_List_Manager::connectionInstance( 'mailrelayemail' );
		if ( $related_api->isConnected() ) {
			$this->setParam( 'new_connection', 1 );
		}

		$this->_directFormHtml( 'mailrelay' );
	}

	/**
	 * just save the key in the database
	 *
	 * @return mixed|void
	 */
	public function readCredentials() {
		$connection = $this->post( 'connection' );
		$key        = ! empty( $connection['key'] ) ? $connection['key'] : '';

		if ( empty( $key ) ) {
			return $this->error( __( 'You must provide a valid MailRelay key', TVE_DASH_TRANSLATE_DOMAIN ) );
		}

		$connection['url'] = isset( $connection['domain'] ) ? $connection['domain'] : $connection['url'];

		$url = ! empty( $connection['url'] ) ? $connection['url'] : '';

		if ( filter_var( $url, FILTER_VALIDATE_URL ) === false || empty( $url ) ) {
			return $this->error( __( 'You must provide a valid MailRelay URL', TVE_DASH_TRANSLATE_DOMAIN ) );
		}

		$this->setCredentials( $connection );

		$result = $this->testConnection();

		if ( $result !== true ) {
			return $this->error( sprintf( __( 'Could not connect to MailRelay using the provided key (<strong>%s</strong>)', TVE_DASH_TRANSLATE_DOMAIN ), $result ) );
		}

		/**
		 * finally, save the connection details
		 */
		$this->save();
		/** @var Thrive_Dash_List_Connection_MailRelayEmail $related_api */
		$related_api = Thrive_Dash_List_Manager::connectionInstance( 'mailrelayemail' );

		if ( isset( $connection['new_connection'] ) && (int) $connection['new_connection'] === 1 ) {
			/**
			 * Try to connect to the email service too
			 */
			$r_result = true;
			if ( ! $related_api->isConnected() ) {
				$_POST['connection'] = $connection;
				$r_result            = $related_api->readCredentials();
			}

			if ( $r_result !== true ) {
				$this->disconnect();

				return $this->error( $r_result );
			}
		} else {
			/**
			 * let's make sure that the api was not edited and disconnect it
			 */
			$related_api->setCredentials( array() );
			Thrive_Dash_List_Manager::save( $related_api );
		}

		return $this->success( __( 'MailRelay connected successfully', TVE_DASH_TRANSLATE_DOMAIN ) );
	}

	/**
	 * test if a connection can be made to the service using the stored credentials
	 *
	 * @return bool|string true for success or error message for failure
	 */
	public function testConnection() {

		/** @var Thrive_Dash_Api_MailRelay $mr */
		$mr = $this->getApi();

		try {
			$mr->get_list();
		} catch ( Thrive_Dash_Api_MailRelay_Exception $e ) {
			return $e->getMessage();
		}

		return true;
	}

	/**
	 * instantiate the API code required for this connection
	 *
	 * @return Thrive_Dash_Api_MailRelay|Thrive_Dash_Api_MailRelayV1
	 */
	protected function _apiInstance() {

		$url     = $this->param( 'url' );
		$api_key = $this->param( 'key' );

		$instance = new Thrive_Dash_Api_MailRelay(
			array(
				'host'   => $url,
				'apiKey' => $api_key,
			)
		);

		if ( false !== strpos( $url, 'ipzmarketing' ) ) {
			$instance = new Thrive_Dash_Api_MailRelayV1( $url, $api_key );
		}

		return $instance;
	}

	/**
	 * get all Subscriber Lists from this API service
	 *
	 * @return array
	 * @throws Thrive_Dash_Api_MailRelay_Exception
	 */
	protected function _getLists() {
		/** @var Thrive_Dash_Api_MailRelay $api */
		$api = $this->getApi();

		$body = $api->get_list();

		$lists = array();
		foreach ( $body as $item ) {
			$lists [] = array(
				'id'   => $item['id'],
				'name' => $item['name'],
			);
		}

		return $lists;
	}

	/**
	 * add a contact to a list
	 *
	 * @param mixed $list_identifier
	 * @param array $arguments
	 *
	 * @return bool|string true for success or string error message for failure
	 */
	public function addSubscriber( $list_identifier, $arguments ) {

		$args = array();
		/** @var Thrive_Dash_Api_MailRelay $api */
		$api = $this->getApi();

		$args['email'] = $arguments['email'];

		if ( ! empty( $arguments['name'] ) ) {
			$args['name'] = $arguments['name'];
		}

		if ( ! empty( $arguments['phone'] ) ) {

			$args['customFields']['f_phone'] = $arguments['phone'];
		}

		try {

			$api->add_subscriber( $list_identifier, $args );

		} catch ( Thrive_Dash_Api_MailRelay_Exception $e ) {
			return $e->getMessage();
		}

		return true;

	}

	/**
	 * Return the connection email merge tag
	 *
	 * @return String
	 */
	public static function getEmailMergeTag() {
		return '[email]';
	}

	/**
	 * disconnect (remove) this API connection
	 */
	public function disconnect() {

		$this->setCredentials( array() );
		Thrive_Dash_List_Manager::save( $this );

		/**
		 * disconnect the email service too
		 */
		$related_api = Thrive_Dash_List_Manager::connectionInstance( 'mailrelayemail' );
		$related_api->setCredentials( array() );
		Thrive_Dash_List_Manager::save( $related_api );

		return $this;
	}

	public function get_automator_autoresponder_fields() {
		 return array( 'mailing_list' );
	}
}
