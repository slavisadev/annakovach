<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

namespace TCB\Integrations\WooCommerce\Shortcodes\MiniCart;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Abstract_Sub_Element
 *
 * @package TCB\Integrations\WooCommerce\Shortcodes\MiniCart
 */
class Abstract_Sub_Element extends \TCB_Element_Abstract {
	/**
	 * All sub elements are not visible
	 *
	 * @return bool
	 */
	public function hide() {
		return true;
	}

	public function has_important_border() {
		return false;
	}

	/**
	 * TODO find a better way to define custom settings
	 *
	 * @param bool $hide_typography
	 * @param bool $important_border
	 *
	 * @return array
	 */
	public function _components( $hide_typography = false, $important_border = false ) {
		$components = $this->general_components();

		$components['layout']['disabled_controls'] = array( 'Display', 'Alignment', '.tve-advanced-controls' );

		$components['animation']        = array( 'hidden' => true );
		$components['responsive']       = array( 'hidden' => true );
		$components['styles-templates'] = array( 'hidden' => true );

		if ( $hide_typography ) {
			$components['typography'] = array( 'hidden' => true );
		} else {
			foreach ( $components['typography']['config'] as $control => $config ) {
				if ( in_array( $control, array( 'css_suffix', 'css_prefix' ) ) ) {
					continue;
				}
				/* typography should apply only on the current element */
				$components['typography']['config'][ $control ]['css_suffix'] = array( '' );
			}
		}

		if ( $this->has_important_border() ) {
			$components['borders']['config']['Borders']['important'] = true;
		}

		$components['layout']['disabled_controls'] = array( 'Display', 'Alignment', '.tve-advanced-controls' );

		return $components;
	}
}
