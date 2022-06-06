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
 * Class Cart_Product_Image
 * @package Thrive\Theme\Integrations\WooCommerce\Elements
 */
class Cart_Product_Image extends WooCommerce\Elements\Abstract_Sub_Element {
	/**
	 * @return string
	 */
	public function name() {
		return __( 'Product Image', THEME_DOMAIN );
	}

	/**
	 * @return string
	 */
	public function identifier() {
		return '.product-thumbnail img';
	}

	/**
	 * @return array
	 */
	public function own_components() {
		$components = parent::own_components();

		$components['layout'] ['disabled_controls'] = [ 'Height', 'Width', 'Alignment' ];

		$components['styles-templates'] = [ 'hidden' => true ];
		$components['responsive']       = [ 'hidden' => true ];
		$components['typography']       = [ 'hidden' => true ];
		$components['background']       = [ 'hidden' => true ];
		$components['animation']        = [ 'hidden' => true ];
		$components['shadow']           = [ 'hidden' => false ];

		$components['typography'] = [ 'hidden' => true ];

		return $components;
	}
}

return new Cart_Product_Image( 'wc-cart-product-image' );
