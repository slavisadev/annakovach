<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

if ( ! class_exists( 'Thrive_Theme_Cloud_Element_Abstract' ) ) {
	require_once ARCHITECT_INTEGRATION_PATH . '/classes/class-thrive-theme-cloud-element-abstract.php';
}

/**
 * Class Thrive_Prev_Next_Element
 */
class Thrive_Prev_Next_Element extends Thrive_Theme_Cloud_Element_Abstract {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Previous/Next Content', THEME_DOMAIN );
	}


	/**
	 * WordPress element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrive_prev_next';
	}

	/**
	 * Show the element only on singular template
	 *
	 * @return bool
	 */
	public function hide() {
		return ! Thrive_Prev_Next::show();
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		$components = parent::own_components();

		$components['thrive_prev_next'] = [
			'config' => [
				'BoxWidth' => [
					'config'  => [
						'default' => '1024',
						'min'     => '100',
						'max'     => '2000',
						'label'   => __( 'Maximum Width', THEME_DOMAIN ),
						'um'      => [ 'px', '%' ],
						'css'     => 'max-width',
					],
					'extends' => 'Slider',
				],
				'Size'     => [
					'config'  => [
						'default' => '20',
						'min'     => '10',
						'max'     => '150',
						'label'   => __( 'Size', THEME_DOMAIN ),
						'um'      => [ 'px', 'em' ],
						'css'     => 'font-size',
					],
					'extends' => 'Slider',
				],
				'NewTab'   => [
					'config'  => [
						'name'    => '',
						'label'   => __( 'Open in new tab', THEME_DOMAIN ),
						'default' => false,
					],
					'extends' => 'Switch',
				],
				'Align'    => [
					'config' => [
						'buttons' => [
							[
								'icon'    => 'a_left',
								'value'   => 'left',
								'tooltip' => __( 'Align Left', THEME_DOMAIN ),
							],
							[
								'icon'    => 'a_center',
								'value'   => 'center',
								'default' => true,
								'tooltip' => __( 'Align Center', THEME_DOMAIN ),
							],
							[
								'icon'    => 'a_right',
								'value'   => 'right',
								'tooltip' => __( 'Align Right', THEME_DOMAIN ),
							],
							[
								'text'    => 'FULL',
								'value'   => 'full',
								'tooltip' => __( 'Full Width', THEME_DOMAIN ),
							],
						],
					],
				],
			],
		];

		$components['decoration'] = [ 'hidden' => true ];
		$components['typography'] = [ 'hidden' => true ];
		$components['layout']     = [ 'hidden' => false ];
		$components['scroll']     = [
			'hidden' => false,
		];

		$components['animation']['disabled_controls'] = [ '.anim-popup', '.anim-link' ];

		return array_merge( $components, $this->group_component() );
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'prev-next';
	}

	/**
	 * Render previous next element
	 *
	 * @return string
	 */
	public function html() {
		return Thrive_Utils::get_element( 'prev-next-element', [], true );
	}

	/**
	 * Group Edit Properties
	 *
	 * @return array|bool
	 */
	public function has_group_editing() {
		return [
			'select_values' => [
				[
					'value'    => 'prev_next_buttons',
					'selector' => '.thrv-prev-next-button',
					'name'     => __( 'Previous/Next Buttons', THEME_DOMAIN ),
					'singular' => __( 'Previous/Next Button', THEME_DOMAIN ),
				],
			],
		];
	}
}

return new Thrive_Prev_Next_Element( 'thrive_prev_next' );
