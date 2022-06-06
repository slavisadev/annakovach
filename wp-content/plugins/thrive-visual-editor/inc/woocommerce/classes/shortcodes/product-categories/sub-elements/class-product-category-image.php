<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

namespace TCB\Integrations\WooCommerce\Shortcodes\Shop;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Product_Category_Image
 *
 * @package TCB\Integrations\WooCommerce\Shortcodes\Shop
 */
class Product_Category_Image extends \TCB_Element_Abstract {

	/**
	 * Element name
	 *
	 * @return string|void
	 */
	public function name() {
		return __( 'Product Category Thumbnail', 'thrive-cb' );
	}

	/**
	 * WordPress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.product-category a img';
	}

	/**
	 * Element is not visible in the sidebar
	 *
	 * @return bool
	 */
	public function hide() {
		return true;
	}

	public function own_components() {
		/* only the layout, borders and shadows are visible */
		$components = array(
			'typography'       => array( 'hidden' => true ),
			'background'       => array( 'hidden' => true ),
			'animation'        => array( 'hidden' => true ),
			'responsive'       => array( 'hidden' => true ),
			'styles-templates' => array( 'hidden' => true ),
			'shadow'           => array(
				'config' => array(
					/* sometimes the 'box-shadow' set from woo can be stronger than this, so we give this an '!important' to help it */
					'important'         => true,
					/* only the drop-shadow makes sense for images, disable the rest */
					'disabled_controls' => array( 'inner', 'text' ),
				),
			),
		);

		$components['layout']['disabled_controls'] = array( 'Display', 'Alignment', '.tve-advanced-controls' );

		return $components;
	}
}

return new Product_Category_Image( 'product-category-image' );
