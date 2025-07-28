<?php
/**
 * Contains the SourceManager class.
 *
 * @since 0.0.7
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use AcfComponentManager\Form\SourceForm;
use AcfComponentManager\View\SourceView;
use AcfComponentManager\NoticeManager;

/**
 * Contains SettingsManager.
 *
 * @since 0.0.1
 */
class SourceManager {

	/**
	 * Settings.
	 *
	 * @since 0.0.7
	 * @access protected
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.7
	 */
	public function __construct() {
		$this->settings = $this->get_settings();
	}

	/**
	 * Add menu tab.
	 *
	 * @since 0.0.7
	 * @param array $tabs Existing tabs.
	 *
	 * @retun array
	 *   The tabs.
	 */
	public function add_menu_tab( array $tabs ): array {
		$tabs['manage_sources'] = __( 'Manage sources', 'acf-component-manager' );
		return $tabs;
	}

	/**
	 * Get Settings.
	 *
	 * @since 0.0.7
	 * @access public
	 *
	 * @return array $settings
	 */
	public function get_settings() {
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
		print '<h2>' . __('Manage Sources', 'acf-component_manager') . '</h2>';

		switch ($action) {
			case 'view':
				$view = new SourceView( $form_url );
				$settings = $this->get_settings();
				$view->view( $settings );
				break;
			case 'add':
			case 'edit':
				$source_id = uniqid();
				if ( isset( $_GET['source_id'] ) ) {
					$source_id = sanitize_text_field( $_GET['source_id'] );
				}
				$form = new SourceForm( $form_url );
				$form->form( $this->get_settings(), $source_id );
				break;
			case 'delete':
				$source_id = uniqid();
				if ( isset( $_GET['source_id'] ) ) {
					$source_id = sanitize_text_field( $_GET['source_id'] );
				}
				$form = new SourceForm( $form_url );
				$form->delete( $this->get_settings(), $source_id );
				break;
		}
	}


	/**
	 * Dashboard.
	 */
	public function dashboard() {
		$settings = $this->get_settings();
		$view = new SourceView( '' );
		$view->dashboard( $settings );
	}

	/**
	 * Source form callback function.
	 *
	 * @param array $form_data The form data submitted.
	 */
	public function save( array $form_data ) {
		$settings = $this->get_settings();
		$source_id = null;
		$source_data = array();
		$notice_manager = new NoticeManager();
		$notice_manager->add_notice( print_r( $form_data, true ), 'error' );

		if ( isset( $form_data['source_id'] ) ) {
			$source_id = sanitize_text_field( $form_data['source_id'] );
		}
		else {
			$notice_manager->add_notice( 'Source id is not set, please try again.', 'error' );
		}
		if ( $source_id && isset( $settings['sources'][$source_id] ) ) {
			$source_data = $settings['sources'][$source_id];
		}

		$enabled = false;
		if ( isset( $form_data['enabled'] ) ) {
			$enabled = $form_data['enabled'];
		}


		if ( isset( $form_data['source_type'] ) ) {
			$source_type = sanitize_text_field( $form_data['source_type'] );
		}
		$source_path = '';
		$source_name = '';
		switch ( $source_type ) {
			case 'parent_theme':
				$source_path = get_template_directory();
				$source_name = wp_get_theme( get_template() )->get( 'Name' );
				break;
			case 'child_theme':
				$source_path = get_stylesheet_directory();
				$source_name = wp_get_theme( get_stylesheet() )->get( 'Name' );
				break;
			default:
				$source_path = WP_PLUGIN_DIR . '/' . $source_type;
				$plugin_data = get_plugin_data( $source_path );
				$source_name = $plugin_data['Name'];
				break;
		}

		$source_data = array(
			'source_type' => $source_type,
			'source_path' => $source_path,
			'source_id' => $source_id,
			'enabled' => $enabled,
			'source_name' => $source_name,
		);

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
		$source_data['components_directory'] = $components_directory;

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
		$source_data['file_directory'] = $file_directory;

		$settings['sources'][$source_id] = $source_data;
		update_option( SETTINGS_OPTION_NAME, $settings );
	}

	/**
	 * Delete source callback function.
	 */
	public function delete( array $form_data ) {
		$settings = $this->get_settings();
		if ( isset( $form_data['source_id'] ) ) {
			$source_id = sanitize_text_field( $form_data['source_id'] );
		}
		if ( isset( $settings['sources'][$source_id] ) ) {
			unset( $settings['sources'][$source_id] );
		}
		update_option( SETTINGS_OPTION_NAME, $settings );
	}

	/**
	 * Get sources.
	 *
	 * @param string $enabled Defaults to true.
	 *
	 * @return array
	 */
	public function get_sources( string $enabled = 'on' ) {
		$settings = $this->get_settings();
		$sources = array();
		if ( array_key_exists( 'sources', $settings ) ) {
			$sources = $settings['sources'];
		}
		if ( $enabled && ! empty( $sources ) ) {
			$sources = array_filter( $sources, function( $source ) {
				return $source['enabled'] === 'on';
			});

		}
		return $sources;
	}
}
