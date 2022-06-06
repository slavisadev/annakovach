<?php

namespace TVE\Dashboard\Automator;

use Thrive\Automator\Items\Data_Object;
use Thrive\Automator\Items\Trigger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Woo_Product_Refund
 */
class Woo_Product_Refund extends Trigger {
	/**
	 * Get the trigger identifier
	 *
	 * @return string
	 */
	public static function get_id() {
		return 'woocommerce/product_refund';
	}

	/**
	 * Get the trigger hook
	 *
	 * @return string
	 */
	public static function get_wp_hook() {
		return 'thrive_woo_product_refund';
	}

	/**
	 * Get the trigger provided params
	 *
	 * @return array
	 */
	public static function get_provided_data_objects() {
		return array( 'woo_product_data', 'user_data' );
	}

	/**
	 * Get the number of params
	 *
	 * @return int
	 */
	public static function get_hook_params_number() {
		return 2;
	}

	/**
	 * Get the name of the app to which the hook belongs
	 *
	 * @return string
	 */
	public static function get_app_name() {
		return 'WooCommerce';
	}

	/**
	 * Get the trigger name
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'WooCommerce product refunded';
	}

	/**
	 * Get the trigger description
	 *
	 * @return string
	 */
	public static function get_description() {
		return 'This trigger will be fired whenever a WooCommerce product is refunded';
	}

	/**
	 * Get the trigger logo
	 *
	 * @return string
	 */
	public static function get_image() {
		return 'woo-refund-order';
	}

	public function process_params( $params = array() ) {
		$data = array();

		if ( ! empty( $params[1] ) ) {
			$data_object_classes = Data_Object::get();

			list ( $product, $user ) = $params;

			$data['user_data']        = empty( $data_object_classes['user_data'] ) ? null : new $data_object_classes['user_data']( $user );
			$data['woo_product_data'] = empty( $data_object_classes['woo_product_data'] ) ? $product : new $data_object_classes['woo_product_data']( $product );
		}

		return $data;
	}

}
