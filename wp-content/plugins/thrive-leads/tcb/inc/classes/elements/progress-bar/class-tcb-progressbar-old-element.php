<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Progressbar_Element
 */
class TCB_Progressbar_Old_Element extends TCB_Element_Abstract {

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Progress Bar', 'thrive-cb' );
	}

	/**
	 * Get element alternate
	 *
	 * @return string
	 */
	public function alternate() {
		return 'progress, fill';
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'progress_bar';
	}

	/**
	 * Section element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv_progress_bar, .thrv-progress-bar';
	}

	public function hide() {
		return true;
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return array(
			'progressbar_old' => array(
				'config' => array(
					'FillPercent'     => array(
						'to'      => '.tve_progress_bar_fill_wrapper',
						'config'  => array(
							'default' => '20',
							'min'     => '0',
							'max'     => '100',
							'label'   => __( 'Fill Percentage', 'thrive-cb' ),
							'um'      => array( '%' ),
							'css'     => 'width',
						),
						'extends' => 'Slider',
					),
					'ExternalFields'  => array(
						'config'  => array(
							'key'               => 'number',
							'shortcode_element' => '.tve_progress_bar_fill_wrapper',
						),
						'extends' => 'CustomFields',
					),
					'FillColor'       => array(
						'to'      => '.tve_progress_bar_fill',
						'config'  => array(
							'default' => '000',
							'label'   => __( 'Fill', 'thrive-cb' ),
							'options' => array(
								'output' => 'object',
							),
						),
						'extends' => 'ColorPicker',
					),
					'LabelColor'      => array(
						'to'      => '.thrv-inline-text',
						'config'  => array(
							'default' => '000',
							'label'   => __( 'Label Color', 'thrive-cb' ),
							'options' => array(
								'output' => 'object',
							),
						),
						'extends' => 'ColorPicker',
					),
					'BackgroundColor' => array(
						'to'      => '.tve-progress-bar',
						'config'  => array(
							'default' => '000',
							'label'   => __( 'Background', 'thrive-cb' ),
							'options' => array(
								'output' => 'object',
							),
						),
						'extends' => 'ColorPicker',
					),
					'InnerLabel'      => array(
						'config'  => array(
							'name'    => '',
							'label'   => __( 'Add Inner Label', 'thrive-cb' ),
							'default' => true,
						),
						'to'      => '.tve-progress-bar-label',
						'extends' => 'Switch',
					),
				),
			),
			'shadow'          => array(
				'config' => array(
					'to'                => '.tve-progress-bar',
					'disabled_controls' => array( 'inner', 'text' ),
				),
			),
			'borders'         => array(
				'config' => array(
					'to'      => '.tve-progress-bar',
					'Borders' => array(),
					'Corners' => array(),
				),
			),
			'typography'      => array( 'hidden' => true ),
			'background'      => array( 'hidden' => true ),
			'animation'       => array( 'hidden' => true ),
			'layout'          => array(
				'disabled_controls' => array(
					'Overflow',
					'ScrollStyle',
				),
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
}
