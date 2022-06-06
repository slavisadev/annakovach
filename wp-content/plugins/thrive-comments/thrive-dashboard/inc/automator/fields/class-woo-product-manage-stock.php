<?php

namespace TVA\Automator;

use Thrive\Automator\Items\Data_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Woo_Product_Manage_Stock
 */
class Woo_Product_Manage_Stock extends Data_Field {
	/**
	 * Field name
	 */
	public static function get_name() {
		return 'Stock managed';
	}

	/**
	 * Field description
	 */
	public static function get_description() {
		return 'Filter by if product manage stock';
	}

	/**
	 * Field input placeholder
	 */
	public static function get_placeholder() {
		return '';
	}

	public static function get_dummy_value() {
		return 'FALSE';
	}

	public static function get_id() {
		return 'product_manage_stock';
	}

	public static function get_supported_filters() {
		return array( 'boolean' );
	}

	public static function get_field_value_type() {
		return static::TYPE_BOOLEAN;
	}
}
