<?php

namespace TVA\Automator;

use Thrive\Automator\Items\Data_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Woo_Product_Width
 */
class Woo_Product_Width extends Data_Field {
	/**
	 * Field name
	 */
	public static function get_name() {
		return 'Product width';
	}

	/**
	 * Field description
	 */
	public static function get_description() {
		return 'Filter by the product width';
	}

	/**
	 * Field input placeholder
	 */
	public static function get_placeholder() {
		return '';
	}

	public static function get_dummy_value() {
		return 23;
	}

	public static function get_id() {
		return 'product_width';
	}

	public static function get_supported_filters() {
		return array( 'number_comparison' );
	}

	public static function get_field_value_type() {
		return static::TYPE_NUMBER;
	}
}
