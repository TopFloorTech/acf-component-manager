<?php

/**
 * @wordpress-plugin
 * Plugin Name: ACF Component Manager
 * Description: Manages ACF based components.
 * Version: 0.0.1
 * Author: Scott Sawyer
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin version.
 */
define( 'ACF_COMPONENT_MANAGER_VERSION', '0.0.1' );

/**
 * Minimum WordPress version.
 */
define( 'ACF_COMPONENT_MANAGER_MIN_WP_VERSION', '6.0' );

/**
 * PHP Minimum version.
 */
define( 'ACF_COMPONENT_MANAGER_MIN_PHP_VERSION', '8.0' );

/**
 * Misc constants.
 */
define( 'ACF_COMPONENT_MANAGER_FILE', __FILE__ );
define( 'ACF_COMPONENT_MANAGER_PATH', trailingslashit( plugin_dir_path( ACF_COMPONENT_MANAGER_FILE ) ) );
define( 'ACF_COMPONENT_MANAGER_ADMIN_ASSETS', trailingslashit( plugin_dir_url( ACF_COMPONENT_MANAGER_FILE ) ) . 'assets/admin/' );

/**
 * Stored components option name.
 */
define( 'STORED_COMPONENTS_OPTION_NAME', 'acf_component_manager_components' );

/**
 * Settings option name.
 */
define( 'SETTINGS_OPTION_NAME', 'acf-component-manager-settings' );

/**
 * Notices option name.
 */
define( 'NOTICES_OPTION_NAME', 'acf-component-manager-notices' );

require_once 'includes/autoloader.php';

/**
 * Activation.
 */
function acf_component_manager_activate() {
	require_once ACF_COMPONENT_MANAGER_PATH . 'src/class-activator.php';
	\AcfComponentManager\Activator::activate();
}

/**
 * Deactivation.
 */
function acf_component_manager_deactivate() {
	require_once ACF_COMPONENT_MANAGER_PATH . 'src/class-deactivator.php';
	\AcfComponentManager\Deactivator::deactivate();
}

register_activation_hook( ACF_COMPONENT_MANAGER_FILE, 'acf_component_manager_activate' );
register_deactivation_hook( ACF_COMPONENT_MANAGER_FILE, 'acf_component_manager_deactivate' );

/**
 * The core plugin class.
 */
require ACF_COMPONENT_MANAGER_PATH . 'src/class-acf-component-manager.php';

/**
 * Begin execution.
 */
function acf_component_manager_run() {
	$plugin = new AcfComponentManager\AcfComponentManager();
	$plugin->run();
}

acf_component_manager_run();
