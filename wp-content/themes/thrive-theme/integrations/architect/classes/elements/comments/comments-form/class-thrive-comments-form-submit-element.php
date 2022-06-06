<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Thrive_Comments_Form_Submit_Element
 */
class Thrive_Comments_Form_Submit_Element extends Thrive_Theme_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Comments Form Submit', THEME_DOMAIN );
	}

	/**
	 * Wordpress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.comment-form .comment-form-submit';
	}

	public function has_hover_state() {
		return true;
	}

	public function hide() {
		return true;
	}

	/**
	 * If an element has a selector, the data-css will not be generated.
	 *
	 * @return bool
	 */
	public function has_selector() {
		return true;
	}

	public function own_components() {

		$controls_default_config = [
			'css_suffix' => ' button',
			'css_prefix' => '',
		];

		return array(
			'thrive_comments_form_submit' => [
				'config' => array(
					'icon_side'   => array(
						'css_suffix' => ' .thrv_icon',
						'css_prefix' => '',
						'config'     => array(
							'name'    => __( 'Icon Side', THEME_DOMAIN ),
							'buttons' => array(
								array(
									'value' => 'left',
									'text'  => __( 'Left', THEME_DOMAIN ),
								),
								array(
									'value' => 'right',
									'text'  => __( 'Right', THEME_DOMAIN ),
								),
							),
						),
					),
					'ButtonWidth' => array(
						'css_prefix' => '',
						'config'     => array(
							'default' => '100',
							'min'     => '10',
							'max'     => '100',
							'label'   => __( 'Button width', THEME_DOMAIN ),
							'um'      => array( '%' ),
							'css'     => 'width',
						),
						'extends'    => 'Slider',
					),
					'ButtonAlign' => array(
						'config'  => array(
							'name'    => __( 'Button Align', THEME_DOMAIN ),
							'buttons' => array(
								array(
									'icon'    => 'a_left',
									'text'    => '',
									'value'   => 'left',
									'default' => true,
								),
								array(
									'icon'  => 'a_center',
									'text'  => '',
									'value' => 'center',
								),
								array(
									'icon'  => 'a_right',
									'text'  => '',
									'value' => 'right',
								),
								array(
									'icon'  => 'a_full-width',
									'text'  => '',
									'value' => 'justify',
								),
							),
						),
						'extends' => 'ButtonGroup',
					),
					'style'       => array(
						'css_suffix' => ' button',
						'css_prefix' => '',
						'config'     => array(
							'label'   => __( 'Style', THEME_DOMAIN ),
							'items'   => array(
								'default'      => __( 'Default', THEME_DOMAIN ),
								'ghost'        => __( 'Ghost', THEME_DOMAIN ),
								'rounded'      => __( 'Rounded', THEME_DOMAIN ),
								'full_rounded' => __( 'Full Rounded', THEME_DOMAIN ),
								'gradient'     => __( 'Gradient', THEME_DOMAIN ),
								'elevated'     => __( 'Elevated', THEME_DOMAIN ),
								'border_1'     => __( 'Border 1', THEME_DOMAIN ),
								'border_2'     => __( 'Border 2', THEME_DOMAIN ),
							),
							'default' => 'default',
						),
					),
				),
			],
			'typography'                  => [
				'config' => [
					'FontSize'      => $controls_default_config,
					'FontColor'     => $controls_default_config,
					'TextAlign'     => $controls_default_config,
					'TextStyle'     => $controls_default_config,
					'TextTransform' => $controls_default_config,
					'FontFace'      => $controls_default_config,
					'LineHeight'    => $controls_default_config,
					'LetterSpacing' => $controls_default_config,
				],
			],
			'layout'                      => [
				'disabled_controls' => [
					'Width',
					'Height',
					'Alignment',
					'.tve-advanced-controls',
				],
				'config'            => [
					'MarginAndPadding' => $controls_default_config
					                      + [
						                      'margin_suffix' => '',
					                      ],
				],
			],
			'borders'                     => [
				'config' => [
					'Borders' => $controls_default_config,
					'Corners' => $controls_default_config,
				],
			],
			'animation'                   => [
				'hidden' => true,
			],
			'responsive'                  => [
				'hidden' => true,
			],
			'background'                  => [
				'config' => [
					'ColorPicker' => $controls_default_config,
					'PreviewList' => $controls_default_config,
				],
			],
			'shadow'                      => [
				'config' => $controls_default_config,
			],
			'styles-templates'            => [
				'hidden' => true,
			],
		);
	}
}

return new Thrive_Comments_Form_Submit_Element( 'thrive_comments_form_submit' );

