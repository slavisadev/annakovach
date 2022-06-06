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
 * Class Thrive_Defaults
 */
class Thrive_Defaults {

	/**
	 * Default skin color pallets
	 *
	 * @return array
	 */
	public static function skin_pallets() {
		return [
			'original'  => [
				0 => [
					'colors'    => [
						0 => [
							'id'    => 0,
							'color' => 'rgb(31, 165, 230)',
						],
						1 => [
							'id'    => 1,
							'color' => 'rgb(53, 105, 180)',
						],
					],
					'gradients' => [],
					'name'      => __( 'Default', THEME_DOMAIN ),
				],
			],
			'modified'  => [
				0 => [
					'colors'    => [
						0 => [
							'id'    => 0,
							'color' => 'rgb(31, 165, 230)',
						],
						1 => [
							'id'    => 1,
							'color' => 'rgb(53, 105, 180)',
						],
					],
					'gradients' => [],
					'name'      => __( 'Default', THEME_DOMAIN ),
				],
			],
			'active_id' => 0,
		];
	}

	/**
	 * Default skin color pallets
	 *
	 * @return array
	 */
	public static function skin_variables() {
		return [
			'colors'    => [
				0 => [
					'id'          => 0,
					'color'       => 'rgb(31, 165, 230)',
					'name'        => 'Main Accent',
					'custom_name' => '1',
					'parent'      => - 1,
				],
				1 => [
					'id'          => 1,
					'color'       => 'rgb(53, 105, 180)',
					'name'        => 'Secondary Accent',
					'custom_name' => '1',
					'parent'      => - 1,
				],
			],
			'gradients' => [],
		];

	}

	/**
	 *  Returns an array of social keys and label names.
	 *
	 * @return array
	 */
	public static function social_labels() {
		return [
			'fb'   => __( 'Facebook Page URL', THEME_DOMAIN ),
			't'    => __( 'Twitter Page URL', THEME_DOMAIN ),
			'pin'  => __( 'Pinterest Page URL', THEME_DOMAIN ),
			'in'   => __( 'Linkedin Page URL', THEME_DOMAIN ),
			'xing' => __( 'Xing Page URL', THEME_DOMAIN ),
			'yt'   => __( 'Youtube Channel URL', THEME_DOMAIN ),
			'ig'   => __( 'Instagram Page URL', THEME_DOMAIN ),
		];
	}

	/**
	 * Theme specific elements label
	 *
	 * @return string
	 */
	public static function theme_group_label() {
		return __( 'Theme Elements', THEME_DOMAIN );
	}

	/**
	 * Template default styles
	 *
	 * @param string $template_class
	 *
	 * @return array
	 */
	public static function template_styles( $template_class = '' ) {
		return [
			'fonts'   => [],
			'css'     => [
				'(min-width: 300px)' => "{$template_class} #wrapper { --header-background-width:100%; --footer-background-width:100%;}{$template_class} .thrv_footer .symbol-section-out { background-color: rgb(241, 241, 241); }{$template_class} .thrv_footer .symbol-section-in { padding: 20px !important; }{$template_class} [data-css=\"tve-u-164d292c1b3\"] > .tcb-flex-col > .tcb-col { justify-content: center; }{$template_class} [data-css=\"tve-u-164d29337f5\"]::after { clear: both; }{$template_class} [data-css=\"tve-u-16b1c7f9088\"] { float: right; }",
				'(min-width: 767px)' => "{$template_class} [data-css=\"tve-u-164d292c1b3\"] { flex-wrap: nowrap !important; }{$template_class} [data-css=\"tve-u-164d294a532\"] { max-width: 40%; }{$template_class} [data-css=\"tve-u-164d294a535\"] { max-width: 60%; }",
			],
			'dynamic' => [],
		];
	}

	/**
	 * Get a default post ID to use as a default post for something. Makes sure there is only one post or $post_type per $scope
	 *
	 * Examples:
	 *  ensure a demo content post post exists for previewing a landing page template
	 *  ensure a page exists that can be automatically set as a "Blog" page
	 *  ensure a page exists that can be automatically set as a "Homepage"
	 *
	 * @param string  $scope               'blog' or 'homepage' - acts as a namespace and ensures the post is unique in that namespace
	 * @param string  $post_title          Title to use when creating the post
	 * @param string  $post_type           Post type of the post being queried
	 * @param boolean $generate_if_missing Generate post if it doesn't exist
	 *
	 * @return int|WP_Error
	 */
	public static function get_default_post_id( $scope = 'blog', $post_title = 'Blog', $post_type = 'page', $generate_if_missing = false ) {
		$opt     = "thrive_default_{$scope}_{$post_type}_id";
		$post_id = get_option( $opt );
		$post    = empty( $post_id ) ? null : get_post( $post_id );

		/* create page if it doesn't exist */
		if ( null === $post || $post->post_type !== $post_type ) {
			if ( $generate_if_missing ) {
				$GLOBALS['thrive_during_post_insert'] = true;

				$post_id = wp_insert_post( [
					'post_parent'  => - 1,
					'post_type'    => $post_type,
					'post_status'  => 'publish',
					'post_content' => "This has been autogenerated as a placeholder for {$scope}.",
					'post_title'   => $post_title,
				] );

				/* make sure we have a unique slug for the newly created post/page */
				wp_update_post( array(
					'ID'          => $post_id,
					'post_status' => 'publish',
					'post_name'   => wp_unique_post_slug( $post_title, $post_id, 'publish', $post_type, 0 ),
				) );

				unset( $GLOBALS['thrive_during_post_insert'] );

				if ( ! is_wp_error( $post_id ) ) {
					update_option( $opt, $post_id );
				}
			} else {
				$post_id = 0;
			}
		} else {
			$post_id = $post->ID;
			/* make sure the post is published */
			if ( $post->post_status !== 'publish' && $generate_if_missing ) {
				wp_update_post( [
					'ID'          => $post_id,
					'post_status' => 'publish',
				] );
			}
		}

		return $post_id;
	}

	/**
	 * Elements that are not available when we only have Architect Light
	 *
	 * @return array
	 */
	public static function unavailable_elements() {
		return [
			'tweet',
			'reveal',
			'countdownevergreen_template',
			'credit',
			'fillcounter',
			'table',
			'callaction',
			'guaranteebox',
			'pricing_table',
		];
	}

	/**
	 * Cards from the TTB Dashboard
	 *
	 * @return array
	 */
	public static function dashboard_card_columns() {
		$theme_templates = [
			[
				'title'        => __( 'Current Theme', THEME_DOMAIN ),
				'icon'         => 'start-current-theme',
				'image'        => '//landingpages.thrivethemes.com/data/skins/thumbnails/thumb-' . thrive_skin()->get_tag() . '.jpg',
				'description'  => '',
				'button_class' => 'no-button',
				'button_text'  => __( 'Manage Themes', THEME_DOMAIN ),
				'extra_class'  => 'ttd-start-current-theme',
				'link'         => '#skins',
				'key'          => 'current',
			],
			[
				'title'        => __( 'Templates', THEME_DOMAIN ),
				'image'        => THEME_URL . '/inc/assets/svg/start-templates.svg',
				'description'  => __( 'Manage your page, text post, video post, audio post, 404, archive and custom theme templates.', THEME_DOMAIN ),
				'button_class' => 'blue-button',
				'button_text'  => __( 'Manage Templates', THEME_DOMAIN ),
				'extra_class'  => 'ttd-start-templates',
				'link'         => '#templates',
			],
		];
		$menu_items      = [
			[
				'title'          => __( 'Theme Wizard', THEME_DOMAIN ),
				'icon'           => 'start-theme-wizard',
				'description'    => __( 'Setup the most important theme templates on your site as quickly as possible.', THEME_DOMAIN ),
				'button_class'   => 'blue-button',
				'button_dynamic' => true,
				'button_text'    => __( 'Start wizard', THEME_DOMAIN ),
				'extra_class'    => 'ttd-start-theme-wizard',
				'link'           => '#wizard',
			],
			[
				'title'        => __( 'Branding', THEME_DOMAIN ),
				'icon'         => 'start-branding',
				'description'  => __( 'Customize your theme to inherit your brand colors, logo and favicon.', THEME_DOMAIN ),
				'button_class' => 'no-button',
				'extra_class'  => 'ttd-start-branding',
				'link'         => '#branding',
			],
			[
				'title'        => __( 'Typography', THEME_DOMAIN ),
				'icon'         => 'start-typography',
				'description'  => __( 'Customize all general typography on your current theme.', THEME_DOMAIN ),
				'button_class' => 'no-button',
				'extra_class'  => 'ttd-start-typography',
				'link'         => '#typography',
			],
			[
				'title'        => __( 'Site Speed', THEME_DOMAIN ),
				'icon'         => 'start-site-speed',
				'description'  => __( 'A performance checklist to ensure you have a blazing fast site.', THEME_DOMAIN ),
				'button_class' => 'no-button',
				'extra_class'  => 'ttd-start-site-speed',
				'link'         => '#performance',
			],
			[
				'title'        => __( 'Help Corner', THEME_DOMAIN ),
				'icon'         => 'start-help-corner',
				'description'  => __( 'Build a better website.', THEME_DOMAIN ),
				'button_class' => 'no-button',
				'extra_class'  => 'ttd-start-help-corner',
				'function'     => 'openHelpCornerModal',
			],
		];

		return [
			[
				'name'            => 'Theme Templates Column',
				'class'           => 'ttd-start-theme-templates-column',
				'items'           => $theme_templates,
			],
			[
				'name'            => 'Menu Items Column',
				'class'           => 'ttd-start-menu-items-column',
				'items'           => $menu_items,
			],
		];
	}
}
