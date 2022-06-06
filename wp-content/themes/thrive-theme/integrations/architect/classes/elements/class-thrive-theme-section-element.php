<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

if ( ! class_exists( 'Thrive_Theme_Cloud_Element_Abstract' ) ) {
	require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-theme-cloud-element-abstract.php';
}

/**
 * Class Thrive_Section_Element
 */
class Thrive_Theme_Section_Element extends Thrive_Theme_Cloud_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Section', THEME_DOMAIN );
	}

	/**
	 * WordPress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.theme-section > div';
	}

	/**
	 * Temporary hide this
	 */
	public function hide() {
		return true;
	}

	/**
	 * Add the theme section component
	 *
	 * @return array
	 */
	public function own_components() {
		$components = parent::own_components();
		$prefix     = tcb_selection_root( false ) . ' ';

		$suffix = [ ' p', ' li', ' label', ' .tcb-plain-text' ];

		$background_selector = '.section-background';
		$content_selector    = '.section-content';

		$components['layout']['config']['MarginAndPadding']['padding_to'] = $content_selector;

		$components['borders']['config']['to'] = $background_selector;
		$components['shadow']['config']['to']  = $background_selector;

		$components['typography']['config']['to']                      = $content_selector;
		$components['typography']['config']['TextStyle']['css_suffix'] = $suffix;

		$components['typography']['config']['FontSize']['css_suffix']   = $suffix;
		$components['typography']['config']['FontSize']['css_prefix']   = $prefix;
		$components['typography']['config']['LineHeight']['css_suffix'] = $suffix;
		$components['typography']['config']['LineHeight']['css_prefix'] = $prefix;
		$components['typography']['config']['FontColor']['css_suffix']  = $suffix;
		$components['typography']['config']['FontColor']['css_prefix']  = $prefix;
		$components['typography']['config']['FontFace']['css_suffix']   = array_merge( $suffix, [ ' h1', ' h2', ' h3', ' h4', ' h5', ' h6' ] );
		$components['typography']['config']['FontFace']['css_prefix']   = $prefix;

		$components['background'] = [
			'config'            => [ 'to' => $background_selector ],
			'disabled_controls' => [],
		];

		$components['animation']  = [ 'hidden' => true ];
		$components['decoration'] = [
			'config' => [ 'to' => $background_selector ],
			'order'  => 50,
		];

		$components['theme_section'] = [
			'config' => [
				'SectionTemplates'   => [
					'config'  => [
						'label' => __( 'Template', THEME_DOMAIN ),
					],
					'extends' => 'ModalPicker',
				],
				'StretchBackground'  => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Stretch background to full width', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'InheritContentSize' => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Inherit content size from layout', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'MinWidth'           => [
					'config'  => [
						'default' => '1080',
						'min'     => '1',
						'max'     => '1980',
						'label'   => __( 'Section minimum width', THEME_DOMAIN ),
						'um'      => [ 'px', '%' ],
						'css'     => 'min-width',
					],
					'extends' => 'Slider',
				],
				'ContentWidth'       => [
					'config'     => [
						'default' => '1080',
						'min'     => '1',
						'max'     => '1980',
						'label'   => __( 'Content width', THEME_DOMAIN ),
						'um'      => [ 'px' ],
						'css'     => 'max-width',
					],
					'css_suffix' => " {$content_selector}",
					'extends'    => 'Slider',
				],
				'StretchContent'     => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Stretch content to fullwidth', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'SectionHeight'      => [
					'config'     => [
						'default' => '80',
						'min'     => '1',
						'max'     => '1000',
						'label'   => __( 'Section minimum height', THEME_DOMAIN ),
						'um'      => [ 'px', 'vh' ],
						'css'     => 'min-height',
					],
					'css_suffix' => " {$content_selector}",
					'extends'    => 'Slider',
				],
				'VerticalPosition'   => [
					'config'     => [
						'name'    => __( 'Vertical position', THEME_DOMAIN ),
						'buttons' => [
							[
								'icon'    => 'top',
								'default' => true,
								'value'   => '',
							],
							[
								'icon'  => 'vertical',
								'value' => 'center',
							],
							[
								'icon'  => 'bot',
								'value' => 'flex-end',
							],
						],
					],
					'css_suffix' => " {$content_selector}",
					'extends'    => 'ButtonGroup',
				],
				'Visibility'         => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Visibility', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'Position'           => [
					'config'  => [
						'name'    => __( 'Position', THEME_DOMAIN ),
						'options' => [
							[
								'name'  => __( 'Right', THEME_DOMAIN ),
								'value' => 'right',
							],
							[
								'name'  => __( 'Left', THEME_DOMAIN ),
								'value' => 'left',
							],
						],
						'default' => 'left',
					],
					'extends' => 'Select',
				],
			],
		];

		$components['sidebar-settings'] = [
			'order'  => 2,
			'config' => [
				'SidebarDisplay'              => [
					'config'  => [
						'name'    => __( 'Display sidebar', THEME_DOMAIN ),
						'buttons' => [
							[
								'text'    => __( 'Normal', THEME_DOMAIN ),
								'default' => true,
								'value'   => 'normal',
							],
							[
								'text'  => __( 'Off screen', THEME_DOMAIN ),
								'value' => 'off-screen',
							],
						],
					],
					'extends' => 'ButtonGroup',
				],
				/* Sticky sidebar settings */
				'Sticky'                      => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Enable sticky sidebar', THEME_DOMAIN ),
						'default' => false,
					],
					'extends' => 'Switch',
				],
				'StickyDelta'                 => [
					'config'  => [
						'default' => '0',
						'min'     => '0',
						'max'     => '240',
						'label'   => __( 'Distance from top or bottom', THEME_DOMAIN ),
						'um'      => [ 'px' ],
					],
					'extends' => 'Slider',
				],
				'StickyUntil'                 => [
					'config'  => [
						'name'    => __( 'Sticky until', THEME_DOMAIN ),
						'options' => [
							[
								'name'  => __( 'End of main container', THEME_DOMAIN ),
								'value' => 'main',
							],
							[
								'name'  => __( 'End of page', THEME_DOMAIN ),
								'value' => 'end',
							],
							[
								'name'  => __( 'It reaches another element', THEME_DOMAIN ),
								'value' => 'element',
							],
						],
						'default' => 'main',
					],
					'extends' => 'Select',
				],
				'StickyElementId'             => [
					'config'  => [
						'label'   => __( 'Element ID', THEME_DOMAIN ),
						'default' => '',
					],
					'extends' => 'LabelInput',
				],

				/* Off screen controls */
				'OffscreenDisplay'            => [
					'config'  => [
						'name'       => __( 'Show sidebar', THEME_DOMAIN ),
						'full-width' => true,
						'buttons'    => [
							[
								'text'    => __( 'Over content', THEME_DOMAIN ),
								'default' => true,
								'value'   => 'slide',
							],
							[
								'text'  => __( 'Push content', THEME_DOMAIN ),
								'value' => 'push',
							],
						],
					],
					'extends' => 'ButtonGroup',
				],
				'OffscreenDefaultState'       => [
					'config'  => [
						'name'    => __( 'Default state', THEME_DOMAIN ),
						'buttons' => [
							[
								'text'    => __( 'Collapsed', THEME_DOMAIN ),
								'default' => true,
								'value'   => 'collapsed',
							],
							[
								'text'  => __( 'Expanded', THEME_DOMAIN ),
								'value' => 'expanded',
							],
						],
					],
					'extends' => 'ButtonGroup',
				],
				'ShowOffscreenInEditor'       => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'View expanded sidebar in editor', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
				'OffscreenOverlayColorSwitch' => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Overlay', THEME_DOMAIN ),
						'default' => false,
					],
					'extends' => 'Switch',
				],
				'OffscreenOverlayColor'       => [
					'config'  => [
						'default' => '000',
						'label'   => __( 'Color', THEME_DOMAIN ),
						'options' => [
							'output' => 'object',
						],
					],
					'extends' => 'ColorPicker',
				],
				'OffscreenDefaultTrigger'     => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Quick toggle icon', THEME_DOMAIN ),
						'default' => true,
						'info'    => true,
					],
					'extends' => 'Switch',
				],
				'OffscreenTriggerPosition'    => [
					'config'  => [
						'name'    => __( 'Position', THEME_DOMAIN ),
						'options' => [
							[
								'name'  => __( 'Top', THEME_DOMAIN ),
								'value' => 'top',
							],
							[
								'name'  => __( 'Center', THEME_DOMAIN ),
								'value' => 'center',
							],
							[
								'name'  => __( 'Bottom', THEME_DOMAIN ),
								'value' => 'bottom',
							],
						],
						'default' => 'left',
					],
					'extends' => 'Select',
				],
				'OffscreenCloseIcon'          => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Close icon', THEME_DOMAIN ),
						'default' => true,
					],
					'extends' => 'Switch',
				],
			],
		];

		return $components;
	}
}

return new Thrive_Theme_Section_Element( 'theme_section' );
