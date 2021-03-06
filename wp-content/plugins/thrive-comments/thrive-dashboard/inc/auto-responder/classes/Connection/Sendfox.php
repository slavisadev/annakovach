<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Thrive_Dash_List_Connection_Sendfox extends Thrive_Dash_List_Connection_Abstract {

	/**
	 * Return api connection title
	 *
	 * @return string
	 */
	public function getTitle() {

		return 'Sendfox';
	}

	/**
	 * Output the setup form html
	 *
	 * @return void
	 */
	public function outputSetupForm() {

		$this->_directFormHtml( 'sendfox' );
	}

	/**
	 * read data from post, test connection and save the details
	 *
	 * show error message on failure
	 *
	 * @return mixed|Thrive_Dash_List_Connection_Abstract
	 */
	public function readCredentials() {

		if ( empty( $_POST['connection']['api_key'] ) ) {
			return $this->error( __( 'Api key is required', TVE_DASH_TRANSLATE_DOMAIN ) );
		}

		$this->setCredentials( $this->post( 'connection' ) );

		$result = $this->testConnection();

		if ( $result !== true ) {
			return $this->error( __( 'Could not connect to Sendfox using provided api key.', TVE_DASH_TRANSLATE_DOMAIN ) );
		}

		/**
		 * finally, save the connection details
		 */
		$this->save();

		return $this->success( __( 'Sendfox connected successfully', TVE_DASH_TRANSLATE_DOMAIN ) );
	}

	/**
	 * @return bool|string
	 */
	public function testConnection() {

		return is_array( $this->_getLists() );
	}

	public function addSubscriber( $list_identifier, $arguments ) {

		try {

			/**
			 * @var $api Thrive_Dash_Api_Sendfox
			 */
			$api = $this->getApi();

			list( $first_name, $last_name ) = $this->_getNameParts( $arguments['name'] );

			$subscriber_args = array(
				'email'      => $arguments['email'],
				'first_name' => $first_name,
				'last_name'  => $last_name,
			);

			$api->addSubscriber( $list_identifier, $subscriber_args );

			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * @return mixed|Thrive_Dash_Api_Sendfox
	 * @throws Exception
	 */
	protected function _apiInstance() {
		$api_key = $this->param( 'api_key' );

		return new Thrive_Dash_Api_Sendfox( $api_key );
	}

	/**
	 * @return array|bool
	 */
	protected function _getLists() {

		$result = array();

		try {

			/**
			 * @var $api Thrive_Dash_Api_Sendfox
			 */
			$api   = $this->getApi();
			$lists = $api->getLists();

			if ( isset( $lists['data'] ) && is_array( $lists['data'] ) ) {
				/* First page of lists */
				$result = $lists['data'];

				/* For multiple pages */
				if ( ! empty( $lists['total'] ) ) {
					$lists_total       = (int) $lists['total'];
					$list_per_page     = (int) $lists['per_page'];
					$pagination_needed = (int) ( $lists_total / $list_per_page ) + 1;

					/* Request pages >=2 and merge lists */
					if ( $pagination_needed >= 2 ) {
						for ( $i = 2; $i <= $pagination_needed; $i ++ ) {
							$response_pages = $api->getListsOnPage( $i );

							if ( isset( $response_pages['data'] ) && is_array( $response_pages['data'] ) ) {
								$result = array_merge( $result, $response_pages['data'] );
							}
						}
					}
				}
			}
		} catch ( Exception $e ) {

		}

		return $result;
	}

	public function get_automator_autoresponder_fields() {
		 return array( 'mailing_list' );
	}
}
