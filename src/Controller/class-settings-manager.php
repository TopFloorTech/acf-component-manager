<?php
/**
 * Contains the SettingsManager class.
 *
 * @package 0.0.1
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use AcfComponentManager\Form\SettingsForm;
use AcfComponentManager\View\ComponentView;
use AcfComponentManager\View\SettingsView;

/**
 * Contains SettingsManager.
 *
 * @since 0.0.1
 */
class SettingsManager {

	/**
	 * Settings.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		$this->settings = $this->get_settings();
	}

	/**
	 * Add menu tab.
	 *
	 * @param array $tabs Existing tabs.
	 *
	 * @retun array
	 *   The tabs.
	 */
	public function add_menu_tab( array $tabs ) : array {
		$tabs['manage_settings'] = __( 'Manage settings', 'acf-component-manager' );
		return $tabs;
	}

	/**
	 * Get Settings.
	 *
	 * @since 0.0.1
	 * @access private
	 *
	 * @return array $settings
	 */
	private function get_settings() {
		$settings = array();
		$stored_settings = get_option( SETTINGS_OPTION_NAME );
		if ( $stored_settings ) {
			$settings = $stored_settings;
		}
		return $settings;
	}

	/**
	 * Render page.
	 *
	 * @since 0.0.1
	 *
	 * @param string $action   The current action.
	 * @param string $form_url The form URL.
	 */
	public function render_page( string $action = 'view', string $form_url = '' ) {
		print '<h2>' . __( 'Manage Settings', 'acf-component_manager' ) . '</h2>';

		switch ( $action ) {
			case 'view':
				$view = new SettingsView( $form_url );
				$settings = $this->get_settings();
				$view->view( $settings );
				break;
			case 'edit':
				$form = new SettingsForm( $form_url );
				$form->form( $this->get_settings() );
				break;
		}
	}

	/**
	 * Dashboard.
	 */
	public function dashboard() {
		$settings = $this->get_settings();
		$view = new SettingsView( '' );
		$view->dashboard( $settings );
	}

	/**
	 * Save form data.
	 *
	 * @since 0.0.1
	 *
	 * @param array $form_data The form data array.
	 */
	public function save( array $form_data ) {
		$dev_mode = false;
		$settings = $this->get_settings();
		if ( isset( $form_data['dev_mode'] ) ) {
			$dev_mode = $form_data['dev_mode'];
		}
		$settings['dev_mode'] = $dev_mode;

		$components_directory = '';
		if ( isset( $form_data['components_directory'] ) ) {
			$components_directory = sanitize_text_field( $form_data['components_directory'] );
			$components_directory_parts = explode( '/', $components_directory );
			$directory_parts = array();
			foreach ( $components_directory_parts as $part ) {
				if ( ! empty( $part ) ) {
					$directory_parts[] = $part;
				}
			}
			$components_directory = implode( '/', $directory_parts );
		}
		$settings['components_directory'] = $components_directory;

		$file_directory = '';
		if ( isset( $form_data['file_directory'] ) ) {
			$file_directory = sanitize_text_field( $form_data['file_directory'] );
			$file_directory_parts = explode( '/', $file_directory );
			$directory_parts = array();
			foreach ( $file_directory_parts as $part ) {
				if ( ! empty( $part ) ) {
					$directory_parts[] = $part;
				}
			}
			$file_directory = implode( '/', $directory_parts );
		}
		$settings['file_directory'] = $file_directory;

		update_option( SETTINGS_OPTION_NAME, $settings );
	}
}
