<?php
/**
 * Deactivates plugin.
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
 * Deactivates plugin.
 */
class Deactivator {

	/**
	 * Deactivates plugin.
	 *
	 * @since    0.0.1
	 */
	public static function deactivate() {
		self::delete_settings();

	}

	/**
	 * Delete settings.
	 *
	 * @since 0.0.1
	 * @access private
	 */
	private static function delete_settings() {
		delete_option( SETTINGS_OPTION_NAME );
		delete_option( STORED_COMPONENTS_OPTION_NAME );
		delete_option( NOTICES_OPTION_NAME );
	}

}
