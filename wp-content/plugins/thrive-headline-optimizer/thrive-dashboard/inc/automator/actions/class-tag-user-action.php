<?php

namespace TVE\Dashboard\Automator;

use Thrive\Automator\Items\Action;
use Thrive\Automator\Items\Action_Field;
use Thrive_Dash_List_Manager;
use function Thrive\Automator\tap_logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Tag_User
 */
class Tag_User extends Action {

	private $autoresponder;

	private $tags;

	private $additional = array();

	/**
	 * Get the action identifier
	 *
	 * @return string
	 */
	public static function get_id() {
		return 'thrive/taguser';
	}

	/**
	 * Get the action name/label
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'Tag user in autoresponder';
	}

	/**
	 * Get the action description
	 *
	 * @return string
	 */
	public static function get_description() {
		return 'Apply {num_items} tags to user in autoresponder';
	}

	/**
	 * Get the action logo
	 *
	 * @return string
	 */
	public static function get_image() {
		return 'tap-tag-user';
	}

	/**
	 * Get the name of app to which action belongs
	 *
	 * @return string
	 */
	public static function get_app_name() {
		return 'Thrive Dashboard';
	}

	public static function get_required_data_objects() {
		return array( 'user_data', 'form_data' );
	}

	/**
	 * Array of action-field keys, required for the action to be setup
	 *
	 * @return array
	 */
	public static function get_required_action_fields() {
		return array( 'autoresponder' => true );
	}

	public static function get_subfields( $field, $selected_value, $action_data ) {
		$api_instance = Thrive_Dash_List_Manager::connectionInstance( $selected_value );
		$fields       = array();
		if ( $api_instance && $api_instance->isConnected() ) {
			$field_keys = $api_instance->get_automator_autoresponder_tag_fields();

			$multiple_option_types = array( 'autocomplete', 'checkbox', 'select' );

			if ( ! empty( $field_keys ) ) {
				$available_fields = Action_Field::get();
				foreach ( $field_keys as $subfield ) {
					$subfield_class = $available_fields[ $subfield ];
					$state_data     = $subfield_class::localize();

					if ( in_array( $subfield_class::get_type(), $multiple_option_types ) ) {
						$state_data['values'] = $subfield_class::get_options_callback( $selected_value );
					}
					$fields[ $state_data['id'] ] = $state_data;
				}
			}
		}

		return $fields;
	}


	public function prepare_data( $data = array() ) {
		if ( ! empty( $data['extra_data'] ) ) {
			$data = $data['extra_data'];
		}

		foreach ( $data['autoresponder']['subfield'] as $key => $subfield ) {
			$this->additional[ $key ] = $subfield['value'];
		}

		$this->autoresponder = $data['autoresponder']['value'];
		if ( ! empty( $data['tag_input']['value'] ) ) {
			$this->tags = $data['tag_input']['value'];
		}//tags were moved as subfields of autoresponder
		elseif ( ! empty( $this->additional['tag_input'] ) ) {
			$this->tags = $this->additional['tag_input'];
		} elseif ( ! empty( $this->additional['tag_select'] ) ) {
			$this->tags = $this->additional['tag_select'];
		}


	}

	public function do_action( $data ) {

		$email = '';
		/**
		 * Filter the data objects that might provided user data
		 */
		$data_sets = apply_filters( 'tvd_automator_api_data_sets', [] );
		/**
		 * Make sure that user_data is always the last item
		 */
		$data_sets   = array_diff( $data_sets, [ 'user_data' ] );
		$data_sets[] = 'user_data';
		while ( ! empty( $data_sets ) && empty( $email ) ) {
			$set = array_shift( $data_sets );

			if ( ! empty( $data[ $set ] ) && $data[ $set ]->can_provide_email() ) {
				$email = $data[ $set ]->get_provided_email();
			}
		}

		if ( empty( $email ) ) {
			return false;
		}

		$apis = Thrive_Dash_List_Manager::getAvailableAPIs( true );
		$api  = $apis[ $this->autoresponder ];

		if ( empty( $api ) ) {
			return false;
		}

		$extra      = [];
		$tags_value = $this->tags;
		if ( is_array( $tags_value ) ) {
			$tags_value = implode( ', ', $tags_value );
		}

		if ( ! empty( $this->additional['mailing_list'] ) ) {
			$extra['list_identifier'] = $this->additional['mailing_list'];
		}

		$api->updateTags( $email, $tags_value, $extra );
	}

	/**
	 * Match all trigger that provice user/form data
	 *
	 * @param $trigger
	 *
	 * @return bool
	 */
	public static function is_compatible_with_trigger( $provided_data_objects ) {
		$action_data_keys = static::get_required_data_objects() ?: array();

		return count( array_intersect( $action_data_keys, $provided_data_objects ) ) > 0;
	}

	public function can_run( $data ) {
		$valid          = true;
		$available_data = array();

		foreach ( static::get_required_data_objects() as $key ) {
			if ( ! empty( $data[ $key ] ) ) {
				$available_data[] = $key;
			}
		}

		if ( empty( $available_data ) ) {
			$valid = false;
			tap_logger()->register( [
				'key'         => static::get_id(),
				'id'          => 'data-not-provided-to-action',
				'message'     => 'Data object required by ' . static::class . ' action is not provided by trigger',
				'class-label' => tap_logger()->get_nice_class_name( static::class ),
			] );
		}

		return $valid;
	}

}
