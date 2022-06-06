<?php
/**
 * Created by PhpStorm.
 * User: Ovidiu
 * Date: 4/18/2017
 * Time: 11:52 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Html_Element
 */
class TCB_Html_Element extends TCB_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Custom HTML', 'thrive-cb' );
	}

	/**
	 * Get element alternate
	 *
	 * @return string
	 */
	public function alternate() {
		return 'code';
	}


	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'custom_html';
	}

	/**
	 * Wordpress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv_custom_html_shortcode'; // For backwards compatibility
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return array(
			'html'       => array(
				'config' => array(),
			),
			'typography' => array( 'hidden' => true ),
			'borders'    => array( 'hidden' => true ),
			'animation'  => array( 'hidden' => true ),
			'background' => array( 'hidden' => true ),
			'shadow'     => array( 'hidden' => true ),
			'layout'     => array(
				'disabled_controls' => array(),
			),
		);
	}

	/**
	 * Element category that will be displayed in the sidebar
	 *
	 * @return string
	 */
	public function category() {
		return static::get_thrive_advanced_label();
	}

	/**
	 * Element info
	 *
	 * @return string|string[][]
	 */
	public function info() {
		return array(
			'instructions' => array(
				'type' => 'help',
				'url'  => 'custom_html',
				'link' => 'https://help.thrivethemes.com/en/articles/4425799-how-to-use-the-custom-html-and-google-map-elements',
			),
		);
	}
}
