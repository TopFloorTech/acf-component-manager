<?php
/**
 * The plugin upgrader.
 *
 * @package acf-component-manager
 *
 * @since 0.0.8
 */

namespace AcfComponentManager;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use AcfComponentManager\Controller\ComponentManager;
use AcfComponentManager\Controller\SettingsManager;
use AcfComponentManager\NoticeManager;

/**
 * Contains Upgrader class.
 */
class Upgrader {

	/**
	 * AcfComponentManager\NoticeManager defintion.
	 *
	 * @since 0.0.7
	 * @access protected
	 * @var \AcfComponentManager\NoticeManager
	 */
	protected $noticeManager;

	/**
	 * AcfComponentManager\Controller\ComponentManager definition.
	 *
	 * @var \AcfComponentManager\Controller\ComponentManager
	 */
	protected $componentManager;

	/**
	 * AcfComponentManager\Controller\SettingsManager definition.
	 *
	 * @var \AcfComponentManager\Controller\SettingsManager
	 */
	protected $setttingsManager;

	/**
	 * Constructs a new Upgrader object.
	 *
	 * @since 0.0.7
	 */
	public function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 0.0.7
	 * @access private
	 */
	private function load_dependencies() {

		$this->noticeManager = new NoticeManager();
		$this->componentManager = new ComponentManager();
		$this->settingsManager = new SettingsManager();
	}

	/**
	 * Runs the upgrade complete hook.
	 *
	 * @param $upgrader_object array
	 * @param $options array
	 * @since 0.0.7
	 * @access public
	 */
	public static function upgrade_complete( array $upgrader_object, array $options ) {
		// The path to our plugin's main file
		$our_plugin = plugin_basename( __FILE__ );
		// If an update has taken place and the updated type is plugins and the plugins element exists
		if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			// Iterate through the plugins being updated and check if ours is there
			foreach( $options['plugins'] as $plugin ) {
				if( $plugin == $our_plugin ) {
					// Set a transient to record that our plugin has just been updated
					set_transient( 'wp_upe_updated', 1 );
				}
			}
		}
	}

	/**
	 * Upgrade 007.
	 *
	 * @since 0.0.7
	 * @param array $settings
	 *
	 * @return array The upgraded options.
	 */
	public function upgrade_007( array $settings ) {
		if ( isset( $settings['version'] ) && $settings['version'] > ACF_COMPONENT_MANAGER_VERSION ) {
			$parent_theme_directory = get_template_directory();
			$child_theme_directory = get_stylesheet_directory();
			// We want to know if we have a parent theme.
			$parent_theme = false;
			if ( $parent_theme_directory !== $child_theme_directory ) {
				$parent_theme = true;
			}

			if ( isset( $settings['active_theme_directory'] ) ) {
					if ( $settings['active_theme_directory'] === $parent_theme_directory ) {
						$new_source = array(
							'source' => 'parent_theme',
							'base_path' => $parent_theme_directory,
							'file_directory' => $settings['file_directory'] ?? '',
							'components_directory' => $settings['components_directory'] ?? '',
						);
					}
					else {
						$new_source = array(
							'source' => 'child_theme',
							'base_path' => $child_theme_directory,
							'file_directory' => $settings['file_directory'] ?? '',
							'components_directory' => $settings['components_directory'] ?? '',
						);
					}
					if ( ! empty( $new_source ) ) {

						// Get any stored components, add the new source key.
						$components = $this->componentManager->get_stored_components();

						if ( ! empty( $components ) ) {
							foreach ( $components as $component ) {
								$component['source'] = $new_source['source'];
							}

							$this->componentManager->set_stored_components( $components );
						}
						$new_settings = array(
							'version' => ACF_COMPONENT_MANAGER_VERSION,
							'dev_mode' => $settings['dev_mode'] ?? false,
							'sources' => $new_source,
						);
						update_option( SETTINGS_OPTION_NAME, $new_settings );

					}
				}
		}
	}
}
