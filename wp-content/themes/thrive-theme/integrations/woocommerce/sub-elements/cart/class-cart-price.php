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
 * Class Cart_Price
 * @package Thrive\Theme\Integrations\WooCommerce\Elements
 */
class Cart_Price extends WooCommerce\Elements\Abstract_Sub_Element {
	/**
	 * @return string
	 */
	public function name() {
		return __( 'Cart Price', THEME_DOMAIN );
	}

	/**
	 * @return string
	 */
	public function identifier() {
		return '.order-total bdi, .cart-subtotal bdi';
	}

	/**
	 * @return array
	 */
	public function own_components() {
		$components = parent::own_components();

		$components['typography'] ['disabled_controls'] = [ 'TextAlign' ];
		$components['layout']['disabled_controls']      = [
			'margin-top',
			'margin-left',
			'margin-bottom',
			'Display',
			'Alignment',
			'.tve-advanced-controls',
			'Width',
			'Height',
		];

		return $components;
	}
}

return new Cart_Price( 'wc-cart-price' );
