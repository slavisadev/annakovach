<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}


class Thrive_Dash_List_Connection_Wordpress extends Thrive_Dash_List_Connection_Abstract {

	protected $api_error_type = 'string';
	private $acf_identifier = 'tve_acf_';

	/**
	 * Set current error type output
	 *
	 * @param string $type
	 *
	 * @return $this
	 */
	public function set_error_type( $type = 'string' ) {
		$this->api_error_type = $type;

		return $this;
	}

	/**
	 * WordPress API Connection is always "connected"
	 *
	 * @return bool
	 */
	public function isConnected() {
		return true;
	}


	/**
	 * Return the connection type
	 *
	 * @return String
	 */
	public static function getType() {
		return 'other';
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return 'WordPress account';
	}

	/**
	 * this requires a special naming here, as it's about wordpress roles, not lists of subscribers
	 *
	 * @return string
	 */
	public function getListSubtitle() {
		return 'Choose the role which should be assigned to your subscribers';
	}


	/**
	 * output the setup form html
	 *
	 * @return void
	 */
	public function outputSetupForm() {
		$this->_directFormHtml( 'wordpress' );
	}

	/**
	 * just save the key in the database
	 *
	 * @return mixed|void
	 */
	public function readCredentials() {
		$registration_disabled = isset( $_POST['registration_disabled'] ) ? sanitize_text_field( $_POST['registration_disabled'] ) : 0;

		$this->setCredentials( array(
			'connected'             => true,
			'registration_disabled' => (int) $registration_disabled,
		) );

		/**
		 * finally, save the connection details
		 */
		$this->save();

		return true;
	}

	/**
	 * test if a connection can be made to the service using the stored credentials
	 *
	 * @return bool|string true for success or error message for failure
	 */
	public function testConnection() {
		/**
		 * wordpress integration is always supported
		 */
		return true;
	}

	/**
	 * instantiate the API code required for this connection
	 *
	 * @return mixed
	 */
	protected function _apiInstance() {
		// no API instance needed here
		return null;
	}

	/**
	 * get all Subscriber Lists from this API service
	 *
	 * @return array
	 */
	protected function _getLists() {

		$roles = array();

		foreach ( $this->_getRoles() as $key => $role_data ) {
			$roles[] = array(
				'id'   => $key,
				'name' => $role_data['name'],
			);
		}

		return $roles;
	}

	public function get_api_custom_fields( $params, $force = false, $get_all = false ) {

		return $this->getAllCustomFields( $force );
	}

	/**
	 * Get all custom fields by list id
	 *
	 * @param $force calls the API and invalidate cache
	 *
	 * @return array|mixed
	 */
	public function getAllCustomFields( $force ) {

		// Serve from cache if exists and requested
		$cached_data = $this->_get_cached_custom_fields();

		if ( false === $force && ! empty( $cached_data ) ) {
			return $cached_data;
		}

		$default_fields = array(
			array(
				'id'    => 'nickname',
				'label' => 'Nickname',
				'name'  => 'nickname',
				'type'  => 'TEXT',
			),
			array(
				'id'    => 'description',
				'label' => 'Biographical Info',
				'name'  => 'description',
				'type'  => 'TEXT',
			),
			array(
				'id'    => 'user_url',
				'label' => 'Website',
				'name'  => 'user_url',
				'type'  => 'TEXT',
			),
		);
		$custom_fields  = array();
		$roles          = $this->_getLists();

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role ) {

				if ( empty( $role['id'] ) ) {
					continue;
				}
				$fields = $default_fields;
				foreach ( tvd_get_acf_user_external_fields( $role['id'] ) as $field ) {
					$fields[ $field['name'] ] = array(
						'id'    => $this->acf_identifier . $field['name'],
						'label' => $field['label'],
						'name'  => $field['name'],
						'type'  => 'TEXT',
					);
				}
				$custom_fields[ $role['id'] ] = $fields;
			}
		}

		$this->_save_custom_fields( $custom_fields );

		return $custom_fields;
	}

	/**
	 * List of accepted roles
	 *
	 * @return array[]
	 */
	protected function _getRoles() {
		/* get_editable_roles only loaded in the admin sections */
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}
		$user_roles = get_editable_roles();

		unset( $user_roles['administrator'], $user_roles['editor'] );

		return $user_roles;
	}

	/**
	 * Construct an error object to be sent as result. Depending on $this->api_error_type, formats the message as a string or an assoc array
	 *
	 * @param string|array $message
	 * @param string $field
	 */
	protected function build_field_error( $message, $field ) {
		if ( $this->api_error_type !== 'string' && ! is_array( $message ) ) {
			$message = array(
				'field' => $field,
				'error' => $message,
			);
		}

		return $this->error( $message );
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
		/**
		 * If current request is not "trusted" ( form settings not saved in the database ), the only accepted role is "subscriber"
		 */
		if ( empty( $arguments['$$trusted'] ) ) {
			$list_identifier = 'subscriber';
		}

		if ( $this->isDisabled() ) {
			return $this->build_field_error( __( 'Registration has been disabled', TVE_DASH_TRANSLATE_DOMAIN ), '' );
		}

		if ( is_user_logged_in() ) {
			return $this->build_field_error( __( 'You are already logged in. Please Logout in order to create a new user.', TVE_DASH_TRANSLATE_DOMAIN ), '' );
		}

		/* Use the same error messages as WordPress */
		if ( empty( $arguments['email'] ) ) {
			return $this->build_field_error( __( '<strong>Error</strong>: Please type your email address.' ), 'email' );
		}

		if ( ! is_email( $arguments['email'] ) ) {
			return $this->build_field_error( __( '<strong>Error</strong>: The email address isn&#8217;t correct.' ), 'email' );
		}

		/* get profile fields from mapping */
		$profile_fields = array( 'first_name', 'last_name', 'nickname', 'description', 'user_url' );
		if ( ! empty( $arguments['tve_mapping'] ) ) {

			foreach ( Thrive_Dash_List_Manager::decodeConnectionString( $arguments['tve_mapping'] ) as $field_name => $spec ) {
				$field_name = str_replace( '[]', '', $field_name );
				if ( ! empty( $spec['wordpress'] ) && strpos( $field_name, 'mapping_' ) !== false ) {
					$arguments[ $spec['wordpress'] ] = $this->processField( $arguments[ $field_name ] );
				}
			}
		}
		$username = $arguments['email'];
		$user_id  = username_exists( $username );

		/**
		 * if we already have this username
		 */
		if ( $user_id ) {
			$username              = $username . rand( 3, 5 );
			$user_id               = null;
			$arguments['username'] = $username;
		}

		/**
		 * check if passwords parameters exist and if they are the same in case they're two
		 */
		if ( isset( $arguments['password'] ) ) {
			if ( isset( $arguments['confirm_password'] ) && $arguments['password'] != $arguments['confirm_password'] ) {
				return $this->error( __( 'Passwords do not match', TVE_DASH_TRANSLATE_DOMAIN ) );
			}

			if ( ! $user_id && email_exists( $arguments['email'] ) == false ) {
				$user_data = apply_filters( 'tvd_create_user_data', array(
					'user_login' => $username,
					'user_pass'  => $arguments['password'],
					'user_email' => $arguments['email'],
				) );

				$user_id = wp_insert_user( $user_data );

			} else {
				return $this->build_field_error( __( '<strong>Error</strong>: This email is already registered. Please choose another one.' ), 'email' );
			}

		} else {
			/* create a sanitized user_login string */
			$sanitized_user_login = trim( sanitize_user( $arguments['email'], true ) );

			$user_id = register_new_user( $sanitized_user_login, $arguments['email'] );
		}

		if ( $user_id instanceof WP_Error ) {
			return $user_id->get_error_message();
		}

		if ( ! empty( $arguments['name'] ) ) {
			list( $arguments['first_name'], $arguments['last_name'] ) = $this->_getNameParts( $arguments['name'] );
		}

		$userdata = array( 'ID' => $user_id );
		foreach ( $profile_fields as $profile_field ) {
			if ( ! empty( $arguments[ $profile_field ] ) ) {
				$userdata[ $profile_field ] = $arguments[ $profile_field ];
				$has_profile_update         = true;
			}
		}
		if ( isset( $has_profile_update ) ) {
			wp_update_user( $userdata );
		}

		if ( tvd_has_external_fields_plugins() ) {
			// if is acf plugin active, run through all of them and update the custom fields for the user
			foreach ( $arguments as $key => $field ) {

				if ( strpos( $key, $this->acf_identifier ) === 0 ) {
					$id = explode( $this->acf_identifier, $key )[1];
					update_field( $id, $field, 'user_' . $user_id );
				}
			}

		}

		if ( isset( $has_profile_update ) ) {
			//WP has an hook for his action which expects 2 parameters
			//we fake the second param
			$old_data            = new stdClass();
			$old_data->user_pass = '';

			do_action( 'profile_update', $user_id, $old_data, $userdata );
		}

		/**
		 * also, assign the selected role to the newly created user
		 */
		$user = new WP_User( $user_id );

		if ( array_key_exists( $list_identifier, $this->_getRoles() ) ) {
			$user->set_role( $list_identifier );
		} else {
			/**
			 * don't let new users get role from what admin had set in WP Settings, because user might have set Administrator role for new users
			 * - in case there is no accepted role
			 */
			$user->set_role( 'subscriber' );
		}

		do_action( 'tvd_after_create_wordpress_account', $user, $arguments );

		return true;

	}

	/**
	 * Get API custom form fields. By default we have only name and phone
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function get_custom_fields( $params = array() ) {
		return array(
			array( 'id' => 'name', 'placeholder' => __( 'Name', 'thrive-cb' ) ),
			array( 'id' => 'password', 'placeholder' => __( 'Password', 'thrive-cb' ) ),
			array( 'id' => 'confirm_password', 'placeholder' => __( 'Confirm password', 'thrive-cb' ) ),
		);
	}

	/**
	 * This cannot be tested
	 *
	 * @return bool
	 */
	public function canTest() {
		return false;
	}

	/**
	 * This cannot be deleted
	 *
	 * @return bool
	 */
	public function canDelete() {
		return false;
	}

	/**
	 * Whether or not registration is currently disabled
	 */
	public function isDisabled() {
		return ! empty( $this->_credentials['registration_disabled'] );
	}

	public function prepareJSON() {
		$message = $this->isDisabled() ? esc_attr__( 'Connection disabled', TVE_DASH_TRANSLATE_DOMAIN ) : esc_attr__( 'Connection enabled', TVE_DASH_TRANSLATE_DOMAIN );

		return parent::prepareJSON() + array(
				'status_icon' => '<span data-tooltip="' . $message . '" class="tvd-api-status-icon tvd-tooltipped status-' . ( $this->isDisabled() ? 'red' : 'green' ) . '"></span>',
			);
	}

	/**
	 * Get localization data needed for setting up this connection within a form
	 *
	 * @return array
	 */
	public function getDataForSetup() {
		/* build an error message */
		$error_message = sprintf(
			__( 'Your connection with WordPress is currently disabled and will not accept registrations. Enable your WordPress connection from the %sAPI dashboard %shere%s', TVE_DASH_TRANSLATE_DOMAIN ),
			'<strong>',
			'<a href="' . admin_url( 'admin.php?page=tve_dash_api_connect#edit/wordpress/autoclose' ) . '" target="_blank">',
			'</a></strong>'
		);

		return array(
			'has_error'  => $this->isDisabled(),
			'error_html' => $error_message,
		);
	}
}
