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
 * Created by PhpStorm.
 * User: Aurelian Pop
 * Date: 06-Jan-16
 * Time: 1:19 PM
 */
class Thrive_Dash_List_Connection_Sendinblue extends Thrive_Dash_List_Connection_Abstract {
	/**
	 * Return the connection type
	 *
	 * @return String
	 */
	public static function getType() {
		return 'autoresponder';
	}

	/**
	 * @return string the API connection title
	 */
	public function getTitle() {
		return 'SendinBlue';
	}

	/**
	 * output the setup form html
	 *
	 * @return void
	 */
	public function outputSetupForm() {
		$related_api = Thrive_Dash_List_Manager::connectionInstance( 'sendinblueemail' );
		if ( $related_api->isConnected() ) {
			$this->setParam( 'new_connection', 1 );
		}
		$this->_directFormHtml( 'sendinblue' );
	}

	/**
	 * should handle: read data from post / get, test connection and save the details
	 *
	 * on error, it should register an error message (and redirect?)
	 */
	public function readCredentials() {
		$ajax_call = defined( 'DOING_AJAX' ) && DOING_AJAX;

		$key = ! empty( $_POST['connection']['key'] ) ? sanitize_text_field( $_POST['connection']['key'] ) : '';

		if ( empty( $key ) ) {
			return $ajax_call ? __( 'You must provide a valid SendinBlue key', TVE_DASH_TRANSLATE_DOMAIN ) : $this->error( __( 'You must provide a valid SendinBlue key', TVE_DASH_TRANSLATE_DOMAIN ) );
		}

		$this->setCredentials( $this->post( 'connection' ) );

		$result = $this->testConnection();

		if ( $result !== true ) {
			return $ajax_call ? sprintf( __( 'Could not connect to SendinBlue using the provided key (<strong>%s</strong>)', TVE_DASH_TRANSLATE_DOMAIN ), $result ) : $this->error( sprintf( __( 'Could not connect to SendinBlue using the provided key (<strong>%s</strong>)', TVE_DASH_TRANSLATE_DOMAIN ), $result ) );
		}

		/**
		 * finally, save the connection details
		 */
		$this->save();

		/** @var Thrive_Dash_List_Connection_SendinblueEmail $related_api */
		$related_api = Thrive_Dash_List_Manager::connectionInstance( 'sendinblueemail' );

		if ( isset( $_POST['connection']['new_connection'] ) && intval( $_POST['connection']['new_connection'] ) === 1 ) {
			/**
			 * Try to connect to the email service too
			 */

			$r_result = true;
			if ( ! $related_api->isConnected() ) {
				$r_result = $related_api->readCredentials();
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

		$this->success( __( 'SendinBlue connected successfully', TVE_DASH_TRANSLATE_DOMAIN ) );

		if ( $ajax_call ) {
			return true;
		}

	}

	/**
	 * test if a connection can be made to the service using the stored credentials
	 *
	 * @return bool|string true for success or error message for failure
	 */
	public function testConnection() {
		$sendinblue = $this->getApi();

		try {
			$sendinblue->get_account();

		} catch ( Exception $e ) {
			return $e->getMessage();
		}

		return true;

		/**
		 * just try getting a list as a connection test
		 */
	}

	/**
	 * instantiate the API code required for this connection
	 *
	 * @return mixed
	 */
	protected function _apiInstance() {
		return new Thrive_Dash_Api_Sendinblue( "https://api.sendinblue.com/v2.0", $this->param( 'key' ) );
	}

	/**
	 * get all Subscriber Lists from this API service
	 *
	 * @return array|bool for error
	 */
	protected function _getLists() {

		/** @var Thrive_Dash_Api_Sendinblue $sendinblue */
		$sendinblue = $this->getApi();

		$data = array(
			"page"       => 1,
			"page_limit" => 50,
		);

		try {
			$lists = array();

			$raw = $sendinblue->get_lists( $data );

			if ( empty( $raw['data'] ) ) {
				return array();
			}

			foreach ( $raw['data'] as $item ) {
				$lists [] = array(
					'id'   => $item['id'],
					'name' => $item['name'],
				);
			}

			return $lists;
		} catch ( Exception $e ) {
			$this->_error = $e->getMessage() . ' ' . __( "Please re-check your API connection details.", TVE_DASH_TRANSLATE_DOMAIN );

			return false;
		}
	}

	/**
	 * add a contact to a list
	 *
	 * @param mixed $list_identifier
	 * @param array $arguments
	 *
	 * @return mixed
	 */
	public function addSubscriber( $list_identifier, $arguments ) {

		if ( ! is_array( $arguments ) ) {
			$arguments = (array) $arguments;
		}

		$merge_tags = array();
		if ( ! empty( $arguments['name'] ) ) {
			list( $first_name, $last_name ) = $this->_getNameParts( $arguments['name'] );
			$merge_tags = array(
				'NAME'      => $first_name,
				'FIRSTNAME' => $first_name,
				'SURNAME'   => $last_name,
				'VORNAME'   => $first_name,
				'NACHNAME'  => $last_name,
				'LASTNAME'  => $last_name,
			);
		}

		/** @var Thrive_Dash_Api_Sendinblue $api */
		$api = $this->getApi();

		if ( ! empty( $arguments['phone'] ) ) {
			// SendinBlue does not accept phone numbers starting with 0 or other special chars
			$the_phone             = ltrim( ( preg_replace( '/[^0-9]/', '', $arguments['phone'] ) ), '0' );
			$merge_tags['SMS']     = $the_phone;
			$merge_tags['PHONE']   = $the_phone;
			$merge_tags['TELEFON'] = $the_phone;
		}

		$data = array(
			'email'      => $arguments['email'],
			'attributes' => array_merge( $merge_tags, $this->_generate_custom_fields( $arguments ) ),
			'listid'     => array( $list_identifier ),
		);

		try {
			$api->create_update_user( $data );

			return true;
		} catch ( Thrive_Dash_Api_SendinBlue_Exception $e ) {
			return $e->getMessage() ? $e->getMessage() : __( 'Unknown SendinBlue Error', TVE_DASH_TRANSLATE_DOMAIN );
		} catch ( Exception $e ) {
			return $e->getMessage() ? $e->getMessage() : __( 'Unknown Error', TVE_DASH_TRANSLATE_DOMAIN );
		}

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
		$related_api = Thrive_Dash_List_Manager::connectionInstance( 'sendinblueemail' );
		$related_api->setCredentials( array() );
		Thrive_Dash_List_Manager::save( $related_api );

		return $this;
	}

	/**
	 * Return the connection email merge tag
	 *
	 * @return String
	 */
	public static function getEmailMergeTag() {
		return '{{ contact.EMAIL }}';
	}

	/**
	 * @param array $params  which may contain `list_id`
	 * @param bool  $force   make a call to API and invalidate cache
	 * @param bool  $get_all where to get lists with their custom fields
	 *
	 * @return array
	 */
	public function get_api_custom_fields( $params, $force = false, $get_all = true ) {

		$cached_data = $this->_get_cached_custom_fields();
		if ( false === $force && ! empty( $cached_data ) ) {
			return $cached_data;
		}

		/** @var Thrive_Dash_Api_Sendinblue $api */
		$api = $this->getApi();

		try {
			$attributes = $api->get_attributes();
		} catch ( Thrive_Dash_Api_SendinBlue_Exception $e ) {
			// Maybe log this
		}

		$custom_fields   = array();
		$excluded_fields = array(
			'NAME',
			'FIRSTNAME',
			'SURNAME',
			'VORNAME',
			'LASTNAME',
			'SMS',
			'PHONE',
			'TELEFON',
		);

		if ( ! empty( $attributes['data']['normal_attributes'] ) ) {
			foreach ( (array) $attributes['data']['normal_attributes'] as $attribute ) {
				if ( ! empty( $attribute['type'] ) && ! in_array( $attribute['name'], $excluded_fields ) && 'text' === $attribute['type'] ) {
					$custom_fields[] = $this->normalize_custom_field( $attribute );
				}
			}
		}

		$this->_save_custom_fields( $custom_fields );

		return $custom_fields;
	}

	/**
	 * @param array $field
	 *
	 * @return array
	 */
	protected function normalize_custom_field( $field = array() ) {

		return array(
			'id'    => ! empty( $field['name'] ) ? $field['name'] : '',
			'name'  => ! empty( $field['name'] ) ? $field['name'] : '',
			'type'  => ! empty( $field['type'] ) ? $field['type'] : '',
			'label' => ! empty( $field['name'] ) ? $field['name'] : '',
		);
	}

	/**
	 * Generate custom fields array
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function _generate_custom_fields( $args ) {
		$custom_fields = $this->get_api_custom_fields( array() );
		$ids           = $this->build_mapped_custom_fields( $args );
		$result        = array();

		foreach ( $ids as $key => $id ) {
			$field = array_filter(
				$custom_fields,
				function ( $item ) use ( $id ) {
					return $item['id'] === $id['value'];
				}
			);

			$field = array_values( $field );

			if ( ! isset( $field[0] ) ) {
				continue;
			}
			$name         = strpos( $id['type'], 'mapping_' ) !== false ? $id['type'] . '_' . $key : $key;
			$cf_form_name = str_replace( '[]', '', $name );

			$result[ $field[0]['name'] ] = $this->processField( $args[ $cf_form_name ] );
		}

		return $result;
	}

	/**
	 * Build mapped custom fields array based on form params
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public function build_mapped_custom_fields( $args ) {
		$mapped_data = array();

		// Should be always base_64 encoded of a serialized array
		if ( empty( $args['tve_mapping'] ) || ! tve_dash_is_bas64_encoded( $args['tve_mapping'] ) || ! is_serialized( base64_decode( $args['tve_mapping'] ) ) ) {
			return $mapped_data;
		}

		$form_data = thrive_safe_unserialize( base64_decode( $args['tve_mapping'] ) );

		$mapped_fields = $this->getMappedFieldsIDs();

		foreach ( $mapped_fields as $mapped_field_name ) {

			// Extract an array with all custom fields (siblings) names from form data
			// {ex: [mapping_url_0, .. mapping_url_n] / [mapping_text_0, .. mapping_text_n]}
			$cf_form_fields = preg_grep( "#^{$mapped_field_name}#i", array_keys( $form_data ) );

			if ( ! empty( $cf_form_fields ) && is_array( $cf_form_fields ) ) {

				foreach ( $cf_form_fields as $cf_form_name ) {
					if ( empty( $form_data[ $cf_form_name ][ $this->_key ] ) ) {
						continue;
					}

					$field_id = str_replace( $mapped_field_name . '_', '', $cf_form_name );

					$mapped_data[ $field_id ] = array(
						'type'  => $mapped_field_name,
						'value' => $form_data[ $cf_form_name ][ $this->_key ],
					);
				}
			}
		}

		return $mapped_data;
	}

	/**
	 * @param       $email
	 * @param array $custom_fields
	 * @param array $extra
	 *
	 * @return int
	 */
	public function addCustomFields( $email, $custom_fields = array(), $extra = array() ) {

		try {
			/** @var Thrive_Dash_Api_Sendinblue $api */
			$api     = $this->getApi();
			$list_id = ! empty( $extra['list_identifier'] ) ? $extra['list_identifier'] : null;
			$args    = array(
				'email' => $email,
			);

			if ( ! empty( $extra['name'] ) ) {
				$args['name'] = $extra['name'];
			}

			$this->addSubscriber( $list_id, $args );

			$args['attributes'] = $this->_prepareCustomFieldsForApi( $custom_fields );

			$subscriber = $api->create_update_user( $args );

			return ! empty( $subscriber['data']['id'] ) ? $subscriber['data']['id'] : 0;

		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Get available custom fields for this api connection
	 *
	 * @param null $list_id
	 *
	 * @return array
	 */
	public function getAvailableCustomFields( $list_id = null ) {

		return $this->get_api_custom_fields( null, true );
	}

	/**
	 * Prepare custom fields for api call
	 *
	 * @param array $custom_fields
	 * @param null  $list_identifier
	 *
	 * @return array
	 */
	public function _prepareCustomFieldsForApi( $custom_fields = array(), $list_identifier = null ) {

		$prepared_fields = array();
		$api_fields      = $this->get_api_custom_fields( null, true );

		foreach ( $api_fields as $field ) {
			foreach ( $custom_fields as $key => $custom_field ) {
				if ( $field['id'] === $key ) {
					$prepared_fields[ $key ] = $custom_field;
				}
			}

			if ( empty( $custom_fields ) ) {
				break;
			}
		}

		return $prepared_fields;
	}

	public function get_automator_autoresponder_fields() {
		 return array( 'mailing_list' );
	}
	/**
	 * Checks if a connection is V3
	 *
	 * @return bool
	 */
	public function is_v3() {
		$is_v3 = $this->param( 'v3' );

		return ! empty( $is_v3 );
	}

	/**
	 * Upgrades a connection from V2 to V3, by generating a V3 key
	 *
	 * @return string
	 */
	public function upgrade() {
		$api              = $this->getApi();
		$api_key          = $this->param( 'key' );
		$upgrade_response = array();
		$related_api = Thrive_Dash_List_Manager::connectionInstance( 'sendinblueemail' );

		try {
			$upgrade_response = $api->upgrade_to_v3( $api_key );
			$new_key          = $upgrade_response['data']['value'];

			$connection = array(
				'v3'             => true,
				'key'            => $new_key,
				'new_connection' => '0',
			);

			$this->setCredentials( $connection );

			/* Update also the credentials of the related api */
			if ( $related_api->isConnected() ) {
				$related_api->setCredentials( $connection );
				$related_api->save();
			}
			$this->save();
		} catch ( Exception $e ) {
			return $e->getMessage();
		}


		return $upgrade_response;
	}
}
