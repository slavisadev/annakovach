<?php
/**
 * Main class for all AJAX requests
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\Logs\LogRepository;
use PixelCaffeine\ProductCatalog\Configuration;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class for all AJAX requests
 *
 * @class AEPC_Admin_Ajax
 */
class AEPC_Admin_Ajax {

	/**
	 * List of all AJAX requests
	 *
	 * @var string[]
	 */
	public static $ajax_actions = array(
		'save_facebook_options',
		'save_tracking_conversion',
		'edit_tracking_conversion',
		'add_custom_audience',
		'edit_custom_audience',
		'duplicate_custom_audience',
		'save_product_catalog',
		'update_product_catalog',
		'delete_product_catalog_feed',
		'refresh_product_catalog_feed',
		'save_product_feed_refresh_interval',
		'get_user_roles',
		'get_standard_events',
		'get_custom_fields',
		'get_languages',
		'get_device_types',
		'get_categories',
		'get_tags',
		'get_posts',
		'get_dpa_params',
		'get_filter_statement',
		'get_currencies',
		'get_account_ids',
		'get_pixel_ids',
		'get_pixel_stats',
		'get_product_catalog_ids',
		'get_product_feed_ids',
		'get_google_categories',
		'load_fb_pixel_box',
		'load_server_side',
		'load_ca_list',
		'load_conversions_list',
		'load_logs_list',
		'load_sidebar',
		'load_product_feed_status',
		'load_product_feed_schedule',
		'refresh_ca_size',
		'clear_transients',
		'reset_fb_connection',
		'dismiss_notice',
		'clear_logs',
	);

	/**
	 * AEPC_Admin_Ajax Constructor.
	 *
	 * @return void
	 */
	public static function init() {

		// Hooks ajax actions.
		foreach ( self::$ajax_actions as $action ) {
			$callback = array( __CLASS__, 'ajax_' . $action );
			if ( is_callable( $callback ) ) {
				add_action( 'wp_ajax_aepc_' . $action, $callback );
			}
		}
	}

	/**
	 * Edit of custom audience
	 *
	 * @return void|null
	 */
	public static function ajax_save_facebook_options() {
		if (
			! current_user_can( 'manage_ads' )
			|| ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) )
		) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::save_facebook_options();

		// Check about errors.
		if ( ! $res || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( AEPC_Admin_Notices::get_notices( 'success' ) );
		}
	}

	/**
	 * Save conversion event tracking
	 *
	 * @return void|null
	 */
	public static function ajax_save_tracking_conversion() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::save_events();

		// Check about errors.
		if ( ! $res || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( AEPC_Admin_Notices::get_notices( 'success' ) );
		}
	}

	/**
	 * Edit conversion event tracking
	 *
	 * @return void|null
	 */
	public static function ajax_edit_tracking_conversion() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::edit_event();

		// Check about errors.
		if ( ! $res || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( AEPC_Admin_Notices::get_notices( 'success' ) );
		}
	}

	/**
	 * Add custom audience
	 *
	 * @return void|null
	 */
	public static function ajax_add_custom_audience() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::save_audience();

		// Check about errors.
		if ( ! $res || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( AEPC_Admin_Notices::get_notices( 'success' ) );
		}
	}

	/**
	 * Edit facebook options
	 *
	 * @return void|null
	 */
	public static function ajax_edit_custom_audience() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::edit_audience();

		// Check about errors.
		if ( ! $res || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( AEPC_Admin_Notices::get_notices( 'success' ) );
		}
	}

	/**
	 * Duplicate facebook options
	 *
	 * @return void|null
	 */
	public static function ajax_duplicate_custom_audience() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::duplicate_audience();

		// Check about errors.
		if ( ! $res || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( AEPC_Admin_Notices::get_notices( 'success' ) );
		}
	}

	/**
	 * Add custom audience
	 *
	 * @return void|null
	 */
	public static function ajax_save_product_catalog() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::save_product_catalog();

		// Check about errors.
		if ( ! $res->is_success() || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( self::get_product_feed_status_html( false, $res->get( 'background_saving' ) ) );
		}
	}

	/**
	 * Update product catalog
	 *
	 * @return void|null
	 */
	public static function ajax_update_product_catalog() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::update_product_catalog();

		// Check about errors.
		if ( $res->get( 'background_saving' ) && ( ! $res->is_success() || AEPC_Admin_Notices::has_notice( 'error' ) ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( self::get_product_feed_status_html( false, $res->get( 'background_saving' ) ) );
		}
	}

	/**
	 * Delete the product catalog feed
	 *
	 * @return void|null
	 */
	public static function ajax_delete_product_catalog_feed() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::delete_product_catalog_feed();

		// Check about errors.
		if ( ! $res->is_success() || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success();
		}
	}

	/**
	 * Refresh the product catalog feed
	 *
	 * @return void|null
	 */
	public static function ajax_refresh_product_catalog_feed() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::refresh_product_catalog_feed();

		// Check about errors.
		if ( $res->get( 'background_saving' ) && ( ! $res->is_success() || AEPC_Admin_Notices::has_notice( 'error' ) ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			wp_send_json_success( self::get_product_feed_status_html( false, $res->get( 'background_saving' ) ) );
		}
	}

	/**
	 * Refresh the product catalog feed
	 *
	 * @return void|null
	 */
	public static function ajax_save_product_feed_refresh_interval() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Perform the edit from handler function already ready to perform the action.
		$res = AEPC_Admin_Handlers::save_product_feed_refresh_interval();

		// Check about errors.
		if ( ! $res || AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			$notices = AEPC_Admin_Notices::get_notices();
			AEPC_Admin_Notices::remove_notices();

			wp_send_json_success( array( 'messages' => $notices ) );
		}
	}

	/**
	 * Send list of all user roles
	 *
	 * @return void|null
	 */
	public static function ajax_get_user_roles() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$roles = get_editable_roles();

		// Map values.
		foreach ( $roles as $role_name => &$role ) {
			$role = array(
				'id'   => $role_name,
				'text' => $role['name'],
			);
		}

		wp_send_json( array_values( $roles ) );
	}

	/**
	 * Send list of all user roles
	 *
	 * @return void|null
	 */
	public static function ajax_get_standard_events() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$events = array();

		// Map values.
		foreach ( AEPC_Track::$standard_events as $event => $args ) {
			if ( in_array( $event, array( 'CustomEvent' ), true ) || strpos( $args, 'value' ) === false ) {
				continue;
			}

			$events[] = array(
				'id'   => $event,
				'text' => $event,
			);
		}

		wp_send_json( array_values( $events ) );
	}

	/**
	 * Send list of all meta keys
	 *
	 * @return void|null
	 */
	public static function ajax_get_custom_fields() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		global $wpdb;

		$post_type_excluded = apply_filters(
			'aepc_list_custom_fields_post_type_excluded',
			array(
				'attachment',
				'nav_menu_item',
				'revision',
			)
		);

		$meta_key_excluded = apply_filters(
			'aepc_list_meta_key_post_type_excluded',
			array(
				'_edit_last',
				'_edit_lock',
				'_featured',
			)
		);

		$post_type_placeholders = implode( ', ', array_fill( 0, count( $post_type_excluded ), '%s' ) );
		$meta_key_placeholders  = implode( ', ', array_fill( 0, count( $meta_key_excluded ), '%s' ) );

		// @codingStandardsIgnoreStart
		$keys = $wpdb->get_col(
			$wpdb->prepare(
				"
			SELECT meta_key
			FROM $wpdb->postmeta pm
			LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id
			WHERE p.post_type NOT IN ( {$post_type_placeholders} )
			AND pm.meta_key NOT IN ( {$meta_key_placeholders} )
			GROUP BY meta_key
			ORDER BY meta_key",
				array_merge( $post_type_excluded, $meta_key_excluded )
			)
		);
		// @codingStandardsIgnoreEnd

		// Format array with key and value as select2 wants.
		foreach ( $keys as &$key ) {
			$key = array(
				'id'   => $key,
				'text' => $key,
			);
		}

		wp_send_json( $keys );
	}

	/**
	 * Send list of all available languages for filters
	 *
	 * @return void|null
	 */
	public static function ajax_get_languages() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		$translations = wp_get_available_translations();

		// Get only ISO code.
		$iso = array();
		foreach ( $translations as $translation ) {
			$id = str_replace( '_', '-', $translation['language'] );

			if ( is_string( $id ) ) {
				$iso[ $id ] = array(
					'id'   => $id,
					'text' => $translation['english_name'],
				);
			}
		}

		// Add default en_US.
		$iso['en-US'] = array(
			'id'   => 'en-US',
			'text' => __( 'English (American)', 'pixel-caffeine' ),
		);

		// Sort.
		ksort( $iso );

		wp_send_json( array_values( $iso ) );
	}

	/**
	 * Send list of all available device types for filters
	 *
	 * @return void|null
	 */
	public static function ajax_get_device_types() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		wp_send_json(
			array(
				array(
					'id'   => 'desktop',
					'text' => __( 'Desktop', 'pixel-caffeine' ),
				),
				array(
					'id'   => 'mobile_iphone',
					'text' => __( 'iPhone', 'pixel-caffeine' ),
				),
				array(
					'id'   => 'mobile_android_phone',
					'text' => __( 'Android Phone', 'pixel-caffeine' ),
				),
				array(
					'id'   => 'mobile_ipad',
					'text' => __( 'iPad', 'pixel-caffeine' ),
				),
				array(
					'id'   => 'mobile_android_tablet',
					'text' => __( 'Android Tablet', 'pixel-caffeine' ),
				),
				array(
					'id'   => 'mobile_windows_phone',
					'text' => __( 'Windows Phone', 'pixel-caffeine' ),
				),
				array(
					'id'   => 'mobile_ipod',
					'text' => __( 'iPod', 'pixel-caffeine' ),
				),
			)
		);
	}

	/**
	 * Send list of all available device types for filters
	 *
	 * @return void|null
	 */
	public static function ajax_get_dpa_params() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$params = array(
			'value',
			'currency',
			'predicted_ltv',
			'content_name',
			'content_category',
			'content_type',
			'content_ids',
			'num_items',
			'search_string',
			'status',
		);

		foreach ( $params as &$param ) {
			$param = array(
				'id'   => $param,
				'text' => $param,
			);
		}

		wp_send_json( $params );
	}

	/**
	 * Send list of all terms divided by taxonomies for categories
	 *
	 * @return void|null
	 */
	public static function ajax_get_categories() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		/**
		 * Set the taxonomies as key of terms.
		 *
		 * @var array<string, WP_Taxonomy> $terms
		 */
		$terms = get_taxonomies( array( 'public' => true ) );

		// Exclude tag taxonomies from categories.
		foreach ( array( 'post_tag', 'product_tag', 'product_shipping_class', 'post_format' ) as $tax ) {
			unset( $terms[ $tax ] );
		}

		// Foreach taxonomy, get the available terms.
		foreach ( $terms as $taxonomy => &$list ) {
			global $wp_version;

			if ( version_compare( $wp_version, '4.5', '<' ) ) {
				$list = get_terms( $taxonomy );
			} else {
				$list = get_terms( array_merge( array( 'taxonomy' => $taxonomy ) ) );
			}

			if ( ! is_array( $list ) ) {
				unset( $terms[ $taxonomy ] );
				continue;
			}

			// Format array for select2.
			foreach ( $list as &$term ) {
				$term = array(
					'id'   => $term->name,
					'text' => $term->name,
				);
			}

			// Add [[any]] on first place.
			$list = array_merge(
				array(
					array(
						'id'   => '[[any]]',
						'text' => '--- ' . __(
							'anything',
							'pixel-caffeine'
						) . ' ---',
					),
				),
				$list
			);
		}

		wp_send_json( $terms );
	}

	/**
	 * Send list of all terms divided by taxonomies for categories
	 *
	 * @return void|null
	 */
	public static function ajax_get_tags() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		/**
		 * Set the taxonomies as key of terms.
		 *
		 * @var array<string, WP_Taxonomy> $terms
		 */
		$terms = get_taxonomies( array( 'public' => true ) );

		// Foreach taxonomy, get the available terms.
		foreach ( $terms as $taxonomy => &$list ) {
			global $wp_version;

			// Return only tag taxonomies.
			if ( ! in_array( $taxonomy, array( 'post_tag', 'product_tag' ), true ) ) {
				unset( $terms[ $taxonomy ] );
				continue;
			}

			if ( version_compare( $wp_version, '4.5', '<' ) ) {
				$list = get_terms( $taxonomy );
			} else {
				$list = get_terms( array_merge( array( 'taxonomy' => $taxonomy ) ) );
			}

			if ( ! is_array( $list ) ) {
				unset( $terms[ $taxonomy ] );
				continue;
			}

			// Format array for select2.
			foreach ( $list as &$term ) {
				$term = array(
					'id'   => $term->name,
					'text' => $term->name,
				);
			}

			// Add [[any]] on first place.
			$list = array_merge(
				array(
					array(
						'id'   => '[[any]]',
						'text' => '--- ' . __(
							'anything',
							'pixel-caffeine'
						) . ' ---',
					),
				),
				$list
			);
		}

		wp_send_json( $terms );
	}

	/**
	 * Send list of all posts divided by post_type
	 *
	 * @return void|null
	 */
	public static function ajax_get_posts() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Set the taxonomies as key of terms.
		$posts = get_post_types( array( 'public' => true ) );

		// Foreach taxonomy, get the available terms.
		foreach ( $posts as $post_type => &$list ) {
			/**
			 * The function get_posts below will return objects array because no return option in the configuration.
			 *
			 * @var WP_Post[] $list
			 */
			// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
			$list = get_posts(
				array(
					'posts_per_page'   => -1,
					'post_type'        => $post_type,
					'suppress_filters' => false,  // Use for performance.
				)
			);

			// Format array for select2.
			foreach ( $list as &$post ) {
				$post = array(
					'id'   => $post->ID,
					'text' => $post->post_title,
				);
			}

			// Add [[any]] on first place.
			$list = array_merge(
				array(
					array(
						'id'   => '[[any]]',
						'text' => '--- ' . __(
							'anything',
							'pixel-caffeine'
						) . ' ---',
					),
				),
				$list
			);
		}

		wp_send_json( $posts );
	}

	/**
	 * Send the ca filter statement
	 *
	 * @return void|null
	 */
	public static function ajax_get_filter_statement() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Make filter array from javascript.
		$filter = array();
		$tmp    = array();

		foreach ( (array) filter_input( INPUT_GET, 'filter', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY ) as $v ) {
			if ( isset( $v['name'], $v['value'] ) ) {
				$bracketed = str_replace( '[]', '', $v['name'] );
				if ( is_string( $bracketed ) ) {
					$tmp[ $bracketed ] = $v['value'];
				}
			}
		}

		// Convert string with brackets to array in php.
		foreach ( $tmp as $key => $val ) {
			$key_parts = preg_split( '/[\[\]]+/', $key, -1, PREG_SPLIT_NO_EMPTY );

			$ref = &$filter;

			while ( $key_parts ) {
				$part = array_shift( $key_parts );

				if ( ! isset( $ref[ $part ] ) ) {
					$ref[ $part ] = array();
				}

				$ref = &$ref[ $part ];
			}

			$ref = $val;
		}

		$tmp = new AEPC_Admin_CA();
		if ( isset( $filter['ca_rule'] ) ) {
			$tmp->add_filter( $filter['ca_rule'] );
		}

		$statements = $tmp->get_human_rule_list( '<em>', '</em>' );

		echo wp_kses( array_pop( $statements ), 'post' );
		die();
	}

	/**
	 * Send all currencies if woocommerce is activated
	 *
	 * @return void|null
	 */
	public static function ajax_get_currencies() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$currencies = array();

		if ( AEPC_Addons_Support::are_detected_addons() ) {
			foreach ( AEPC_Currency::get_currencies() as $currency => $args ) {
				$currencies[] = array(
					'id'   => esc_attr( $currency ),
					'text' => $args->symbol . ' (' . $args->name . ')',
				);
			}
		}

		wp_send_json( $currencies );
	}

	/**
	 * Send all account ids of user
	 *
	 * @return void|null
	 */
	public static function ajax_get_account_ids() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Get account ids from facebook.
		try {
			$fb       = AEPC_Admin::$api;
			$accounts = $fb->get_account_ids();

			// Format for select2 component.
			foreach ( $accounts as &$account ) {
				$account = array(
					'id'   => wp_json_encode(
						array(
							'id'   => $account->account_id,
							'name' => $account->name,
						)
					) ?: '{}',
					'text' => $account->name . ' (#' . $account->account_id . ')',
				);
			}

			wp_send_json( $accounts );

		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Send all pixel of an account id
	 *
	 * @return void
	 */
	public static function ajax_get_pixel_ids() {
		if (
			! current_user_can( 'manage_ads' )
			|| ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) )
			|| ! filter_has_var( INPUT_GET, 'account_id' )
		) {
			wp_send_json_error( 'Not authorized.' );
		}

		// Get pixel ids from facebook.
		try {
			$fb     = AEPC_Admin::$api;
			$pixels = (array) $fb->get_pixel_ids( filter_input( INPUT_GET, 'account_id', FILTER_SANITIZE_STRING ) );

			// Format for select2 component.
			foreach ( $pixels as &$pixel ) {
				$pixel = array(
					'id'   => wp_json_encode(
						array(
							'id'   => $pixel->id,
							'name' => $pixel->name,
						)
					) ?: '{}',
					'text' => $pixel->name . ' (#' . $pixel->id . ')',
				);
			}

			wp_send_json( $pixels );

		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Send the statistics of a pixel, get by facebook
	 *
	 * @return void|null
	 */
	public static function ajax_get_pixel_stats() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$sets = AEPC_Admin::get_pixel_stats_sets();

		if ( is_wp_error( $sets ) ) {
			wp_send_json_error( $sets );
		} else {
			wp_send_json( $sets );
		}
	}

	/**
	 * Send all pixel of an account id
	 *
	 * @return void|null
	 */
	public static function ajax_get_product_catalog_ids() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Get pixel ids from facebook.
		try {
			$fb               = AEPC_Admin::$api;
			$product_catalogs = (array) $fb->get_product_catalogs( AEPC_Admin::$api->get_business_id() );

			// Format for select2 component.
			foreach ( $product_catalogs as &$product_catalog ) {
				$product_catalog = array(
					'id'   => $product_catalog->id,
					'name' => $product_catalog->name,
				);
			}

			wp_send_json_success( (array) $product_catalogs );

		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Send all product feed ids of product catalog id
	 *
	 * @return void|null
	 */
	public static function ajax_get_product_feed_ids() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		// Get pixel ids from facebook.
		try {
			$fb            = AEPC_Admin::$api;
			$product_feeds = (array) $fb->get_product_feeds( filter_input( INPUT_GET, 'product_catalog_id', FILTER_SANITIZE_STRING ) );

			// Format for select2 component.
			foreach ( $product_feeds as &$product_feed ) {
				$product_feed = array(
					'id'   => $product_feed->id,
					'name' => $product_feed->name,
				);
			}

			wp_send_json_success( (array) $product_feeds );

		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Send the statistics of a pixel, get by facebook
	 *
	 * @return void
	 */
	public static function ajax_get_google_categories() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			wp_send_json_error( 'Not authorized' );
		}

		try {
			$google_categories = AEPC_Admin::$product_catalogs_service->get_google_categories();
			$parents           = filter_input( INPUT_POST, 'parents', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

			// Get the only items of the parent specified.
			foreach ( $parents as $parent_child ) {
				$google_categories = $google_categories[ $parent_child ];
			}

			$response = array_keys( $google_categories );

			wp_send_json( $response );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Load the facebook pixel box on settings page
	 *
	 * @return void|null
	 */
	public static function ajax_load_fb_pixel_box() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$page = AEPC_Admin::get_page( 'general-settings' );

		ob_start();
		$page->get_template_part( 'panels/set-facebook-pixel', array( 'fb' => AEPC_Admin::$api ) );
		$html = ob_get_clean();

		// Don't need notices.
		AEPC_Admin_Notices::remove_notices();

		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
	}

	/**
	 * Load the facebook pixel box on settings page
	 *
	 * @return void|null
	 */
	public static function ajax_load_server_side() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$page = AEPC_Admin::get_page( 'general-settings' );

		ob_start();
		$page->get_template_part( 'panels/server-side-settings', array( 'fb' => AEPC_Admin::$api ) );
		$html = ob_get_clean();

		// Don't need notices.
		AEPC_Admin_Notices::remove_notices();

		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
	}

	/**
	 * Load the custom audiences table list
	 *
	 * @return void|null
	 */
	public static function ajax_load_ca_list() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$page = AEPC_Admin::get_page( 'custom-audiences' );

		ob_start();
		$page->get_template_part( 'tables/ca-list' );
		$html = ob_get_clean();

		// Don't need notices.
		$notices = AEPC_Admin_Notices::get_notices();
		AEPC_Admin_Notices::remove_notices();

		wp_send_json_success(
			array(
				'html'     => $html,
				'messages' => $notices,
			)
		);
	}

	/**
	 * Load the conversions table list
	 *
	 * @return void|null
	 */
	public static function ajax_load_conversions_list() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$page = AEPC_Admin::get_page( 'conversions' );

		ob_start();
		$page->get_template_part( 'tables/ce-tracking' );
		$html = ob_get_clean();

		// Don't need notices.
		$notices = AEPC_Admin_Notices::get_notices();
		AEPC_Admin_Notices::remove_notices();

		wp_send_json_success(
			array(
				'html'     => $html,
				'messages' => $notices,
			)
		);
	}

	/**
	 * Load the conversions table list
	 *
	 * @return void|null
	 */
	public static function ajax_load_logs_list() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$page = AEPC_Admin::get_page( 'logs' );

		ob_start();
		$page->get_template_part( 'tables/log-list' );
		$html = ob_get_clean();

		// Don't need notices.
		$notices = AEPC_Admin_Notices::get_notices();
		AEPC_Admin_Notices::remove_notices();

		wp_send_json_success(
			array(
				'html'     => $html,
				'messages' => $notices,
			)
		);
	}

	/**
	 * Load the news widget on admin sidebar
	 *
	 * @return void|null
	 */
	public static function ajax_load_sidebar() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$page = AEPC_Admin::get_page( 'general-settings' );

		ob_start();
		$page->get_template_part( 'sidebar' );
		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
	}

	/**
	 * Load the product catalog status box
	 *
	 * @return void|null
	 */
	public static function ajax_load_product_feed_status() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		wp_send_json_success( self::get_product_feed_status_html() );
	}

	/**
	 * Load the product feed schedule options
	 *
	 * @return void|null
	 */
	public static function ajax_load_product_feed_schedule() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$page = AEPC_Admin::get_page( 'product-catalog' );

		ob_start();
		$page->get_form_fields(
			'sub/schedule',
			array(
				'group'           => Configuration::VALUE_FB_ACTION_UPDATE,
				'product_feed_id' => filter_input( INPUT_GET, 'product_feed_id', FILTER_SANITIZE_STRING ),
			)
		);
		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
	}

	/**
	 * Refresh custom audience data after click on sync data
	 *
	 * @return void|null
	 */
	public static function ajax_refresh_ca_size() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		try {
			$ca_id = intval( filter_input( INPUT_GET, 'ca_id', FILTER_SANITIZE_NUMBER_INT ) ) ?: 0;
			$ca    = new AEPC_Admin_CA( $ca_id );
			$ca->refresh_size();

			wp_send_json_success();

		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'message' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Clear the transients
	 *
	 * @return void|null
	 */
	public static function ajax_clear_transients() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		AEPC_Admin::clear_transients();

		wp_send_json_success( array( 'message' => __( 'Transients cleared correctly!', 'pixel-caffeine' ) ) );
	}

	/**
	 * Remove the access token option in order to reset facebook connection
	 *
	 * @return void|null
	 */
	public static function ajax_reset_fb_connection() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		AEPC_Admin::reset_fb_connection();

		wp_send_json_success( array( 'message' => __( 'Facebook connection reset correctly!', 'pixel-caffeine' ) ) );
	}

	/**
	 * Dismiss the notices
	 *
	 * @return void|null
	 */
	public static function ajax_dismiss_notice() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		AEPC_Admin_Notices::dismiss_notice( filter_input( INPUT_GET, 'notice_id', FILTER_SANITIZE_STRING ) );

		wp_send_json_success( array( 'message' => __( 'OK', 'pixel-caffeine' ) ) );
	}

	/**
	 * Add custom audience
	 *
	 * @return void|null
	 */
	public static function ajax_clear_logs() {
		if ( ! current_user_can( 'manage_ads' ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING ), str_replace( 'ajax_', '', __FUNCTION__ ) ) ) {
			return null;
		}

		$log_repository = new LogRepository();
		$log_repository->removeAll();

		// Check about errors.
		if ( AEPC_Admin_Notices::has_notice( 'error' ) ) {
			$notices = AEPC_Admin_Notices::get_notices( 'error' );

			// Do not save notices.
			AEPC_Admin_Notices::remove_notices( 'error' );

			wp_send_json_error( $notices );
		} else {
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'All logs are removed.', 'pixel-caffeine' ) );
			wp_send_json_success( self::get_logs_list_html() );
		}
	}

	/**
	 * HELPERS
	 */

	/**
	 * Retrieve the product feed status in HTML format
	 *
	 * @param false $force_updating_status Set true if you want to force the "updating" status.
	 * @param false $force_refreshing_status Set true if you want to force the "refreshing" status.
	 *
	 * @return array
	 */
	protected static function get_product_feed_status_html( $force_updating_status = false, $force_refreshing_status = false ) {
		$page            = AEPC_Admin::get_page( 'product-catalog' );
		$product_catalog = $page->get_product_catalog();

		ob_start();
		$page->get_template_part(
			'panels/product-feed-status',
			array(
				'product_catalog'  => $product_catalog,
				'force_updating'   => $force_updating_status,
				'force_refreshing' => $force_refreshing_status,
			)
		);
		$html = ob_get_clean();

		// Don't need notices.
		$notices = AEPC_Admin_Notices::get_notices();
		AEPC_Admin_Notices::remove_notices();

		return array(
			'html'     => $html,
			'fragment' => 'product_feed_status',
			'messages' => $notices,
		);
	}

	/**
	 * Get the HTML for the logs list
	 *
	 * @return array
	 */
	protected static function get_logs_list_html() {
		$page = AEPC_Admin::get_page( 'logs' );

		ob_start();
		$page->get_template_part( 'tables/log-list' );
		$html = ob_get_clean();

		// Don't need notices.
		$notices = AEPC_Admin_Notices::get_notices();
		AEPC_Admin_Notices::remove_notices();

		return array(
			'html'     => $html,
			'fragment' => 'logs_list',
			'messages' => $notices,
		);
	}

}
