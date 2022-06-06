<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

namespace TCB\Integrations\Automator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Main
 *
 * @package TCB\Integrations\Automator
 */
class Main {

	/**
	 * Add WooCommerce support
	 * process trigger callback
	 *
	 */
	public static function init() {
		self::add_hooks();
	}

	/**
	 * @param string $subpath
	 *
	 * @return string
	 */
	public static function get_integration_path( $subpath = '' ) {
		return TVE_TCB_ROOT_PATH . 'inc/automator/' . $subpath;
	}

	public static function add_hooks() {
		self::load_data_objects();
		self::load_fields();
		self::load_action_fields();
		self::load_actions();
		self::load_triggers();
		add_action( 'tap_output_extra_svg', array( 'TCB\Integrations\Automator\Main', 'display_icons' ) );

		add_filter( 'tvd_automator_api_data_sets', array( 'TCB\Integrations\Automator\Main', 'dashboard_sets' ), 1, 1 );
	}

	public static function load_triggers() {
		foreach ( static::load_files( 'triggers' ) as $trigger ) {
			\thrive_automator_register_trigger( new $trigger() );
		}
	}

	public static function load_actions() {
		foreach ( static::load_files( 'actions' ) as $action ) {
			\thrive_automator_register_action( new $action() );
		}
	}

	public static function load_action_fields() {
		foreach ( static::load_files( 'action-fields' ) as $field ) {
			\thrive_automator_register_action_field( new $field() );
		}
	}

	public static function load_fields() {
		foreach ( static::load_files( 'fields' ) as $field ) {
			\thrive_automator_register_data_field( new $field() );
		}
	}

	public static function load_data_objects() {
		foreach ( static::load_files( 'data-objects' ) as $data_object ) {
			\thrive_automator_register_data_object( new $data_object() );
		}
	}

	public static function display_icons() {
		include_once static::get_integration_path( 'icons.svg' );
	}

	public static function load_files( $type ) {
		$integration_path = static::get_integration_path( $type );

		$local_classes = array();
		foreach ( glob( $integration_path . '/*.php' ) as $file ) {
			require_once $file;
			$class = 'TCB\Integrations\Automator\\' . self::get_class_name_from_filename( $file );
			if ( class_exists( $class ) ) {
				$local_classes[] = $class;
			}
		}

		return $local_classes;
	}

	public static function get_class_name_from_filename( $filename ) {
		$name = str_replace( [ 'class-' ], '', basename( $filename, '.php' ) );

		return str_replace( '-', '_', ucwords( $name, '-' ) );
	}

	/**
	 * Enroll form_data as data that can be used in TD for Automator actions
	 *
	 * @param $sets
	 *
	 * @return mixed
	 */
	public static function dashboard_sets( $sets ) {
		$sets[] = 'form_data';

		return $sets;
	}
}
