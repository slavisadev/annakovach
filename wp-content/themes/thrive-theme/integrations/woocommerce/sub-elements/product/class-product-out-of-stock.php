<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

namespace Thrive\Theme\Integrations\WooCommerce\Elements;
use Thrive\Theme\Integrations\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Product_Out_Of_Stock
 * @package Thrive\Theme\Integrations\WooCommerce\Elements
 */
class Product_Out_Of_Stock extends WooCommerce\Elements\Abstract_Sub_Element {
	/**
	 * @return string
	 */
	public function name() {
		return __( 'Product Out of Stock', THEME_DOMAIN );
	}

	/**
	 * @return string
	 */
	public function identifier() {
		return '.stock';
	}
}

return new Product_Out_Of_Stock( 'wc-product-out-of-stock' );
