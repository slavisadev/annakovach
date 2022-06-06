<?php

namespace TVE\Dashboard\Automator;

use Thrive\Automator\Items\Data_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Woo_Order_Payment_Method_Title
 */
class Woo_Order_Payment_Method_Title extends Data_Field {
	/**
	 * Field name
	 */
	public static function get_name() {
		return 'Payment method title';
	}

	/**
	 * Field description
	 */
	public static function get_description() {
		return 'Filter by payment method title';
	}

	/**
	 * Field input placeholder
	 */
	public static function get_placeholder() {
		return '';
	}

	public static function get_dummy_value() {
		return 'PayPal';
	}

	public static function get_id() {
		return 'payment_method_title';
	}

	public static function get_supported_filters() {
		return array( 'string_ec' );
	}

	public static function get_validators() {
		return array( 'required' );
	}

	public static function get_field_value_type() {
		return static::TYPE_STRING;
	}
}
