<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

namespace TCB\ConditionalDisplay\Conditions;

use TCB\ConditionalDisplay\Condition;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Date_And_Time_Picker extends Condition {
	/**
	 * @return string
	 */
	public static function get_key() {
		return 'date_and_time';
	}

	/**
	 * @return string
	 */
	public static function get_label() {
		return esc_html__( 'Date and time comparison', 'thrive-cb' );
	}

	public function apply( $data ) {
		$compared_value = $this->get_value();
		$field_value    = $data['field_value'];

		switch ( $this->get_operator() ) {
			case 'before':
				$result = strtotime( $field_value ) < strtotime( $compared_value );
				break;
			case 'after':
				$result = strtotime( $field_value ) > strtotime( $compared_value );
				break;
			case 'equals':
				$result = strtotime( date( 'Y/m/d', strtotime( $field_value ) ) ) === strtotime( date( 'Y/m/d', strtotime( $compared_value ) ) );
				break;
			default:
				$result = false;
		}

		return $result;
	}

	public static function get_operators() {
		return [
			'equals' => [
				'label' => 'equals',
			],
			'before' => [
				'label' => 'before',
			],
			'after'  => [
				'label' => 'after',
			],
		];
	}

	public static function get_control_type() {
		return 'date-and-time';
	}

	public static function get_display_size() {
		return 'medium';
	}

	/**
	 * @return array
	 */
	public static function get_validation_data() {
		return [
			'min_minutes' => 0,
			'max_minutes' => 59,
			'min_hours'   => 0,
			'max_hours'   => 23,
		];
	}
}
