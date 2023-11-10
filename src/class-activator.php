<?php
/**
 * The plugin activator.
 *
 * @package acf-component-manager
 *
 * @since 0.0.1
 */

namespace AcfComponentManager;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains Activator class.
 */
class Activator {

	/**
	 * Activation options.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var array $activation_options
	 */
	protected static $activation_options;

	/**
	 * Is activated.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var bool $is_activated
	 */
	protected static $is_activated;

	/**
	 * Min PHP version.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var string $min_php_version
	 */
	protected static $min_php_version;

	/**
	 * Min WordPress version.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var string $min_wordpress_version
	 */
	protected static $min_wordpress_version;

	/**
	 * Activates Plugin
	 *
	 * Doesn't do anything for this plugin.
	 *
	 * @since    0.0.1
	 */
	public static function activate() {

		self::$min_php_version = ACF_COMPONENT_MANAGER_MIN_PHP_VERSION;
		self::$min_wordpress_version = ACF_COMPONENT_MANAGER_MIN_WP_VERSION;

		$requirements = self::check_requirements();
		if ( ! $requirements ) {
			die( __( 'ACF Component Manager not activated!  Your site does not meet the minimum requirements. Minimum Requirements: PHP Version: ' . self::$min_php_version . ', WordPress Version: ' . self::$min_wordpress_version, 'acf-component-manager' ) );
		}

		$setUp = self::set_up();
	}

	/**
	 * Check if plugin is activated.
	 *
	 * @since 0.0.1
	 *
	 * @return bool Whether the plugin is active.
	 */
	private static function is_activated() {

		return is_plugin_active( 'acf-component-manager' );
	}

	/**
	 * Setup Plugin.
	 *
	 * @since 0.0.1
	 * @access private
	 *
	 * @return void
	 */
	private static function set_up() {
		$dev_mode = false;
		if ( wp_get_environment_type() == 'development' ) {
			$dev_mode = true;
		}
		$settings = array(
			'version' => ACF_COMPONENT_MANAGER_VERSION,
			'dev_mode' => $dev_mode,
			'active_theme_directory' => get_stylesheet_directory(),
			'components_directory' => 'components',
			'file_directory' => 'assets',
		);
		update_option( 'acf-component-manager-settings', $settings );
	}

	/**
	 * Check requirements.
	 *
	 * @since 0.0.1
	 * @access private
	 *
	 * @return bool
	 */
	private static function check_requirements() {
		$php = self::check_php_version();
		if ( ! $php ) {
			return false;
		}
		$wordpress = self::check_wordpress_version();
		if ( ! $wordpress ) {
			return false;
		}
		return true;
	}


	/**
	 * Check min WordPress version.
	 *
	 * @since 0.0.1
	 * @access private
	 *
	 * @return bool
	 */
	private static function check_wordpress_version() {
		global $wp_version;
		if ( $wp_version < self::$min_wordpress_version ) {
			return false;
		}
		return true;
	}

	/**
	 * Check minimum PHP version.
	 *
	 * @since 0.0.1
	 * @access private
	 *
	 * @return bool
	 */
	private static function check_php_version() {
		$current_version = phpversion();
		return version_compare( $current_version, self::$min_php_version, '>=' );
	}
}
