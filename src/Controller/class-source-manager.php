<?php
/**
 * Contains the SourceManager class.
 *
 * @since 0.0.7
 * @package acf-component-manager
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use AcfComponentManager\Form\SourceForm;
use AcfComponentManager\Service\SourceService;
use AcfComponentManager\View\SourceView;
use AcfComponentManager\NoticeManager;

/**
 * Contains SettingsManager.
 *
 * @since 0.0.1
 */
class SourceManager {

	/**
	 * AcfComponentManager\NoticeManager definition.
	 *
	 * @since 0.0.7
	 * @var \AcfComponentManager\NoticeManager
	 */
	protected NoticeManager $noticeManager;

	/**
	 * AcfComponentManager\Service\SourceService definition.
	 *
	 * @since 0.0.7
	 * @var \AcfComponentManager\Service\SourceService
	 */
	protected SourceService $sourceService;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.7
	 */
	public function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 0.0.7
	 */
	protected function load_dependencies() {
		$this->noticeManager = new NoticeManager();
		$this->sourceService = new SourceService();
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
	 * Render page.
	 *
	 * @since 0.0.1
	 *
	 * @param string $action   The current action.
	 * @param string $form_url The form URL.
	 */
	public function render_page( string $action = 'view', string $form_url = '' ) {
		print '<h2>' . __( 'Manage Sources', 'acf-component_manager' ) . '</h2>';

		switch ( $action ) {
			case 'view':
				$view = new SourceView( $form_url );
				$stored_sources = $this->sourceService->get_sources( false );
				$view->view( $stored_sources );
				break;
			case 'add':
			case 'edit':
				$source_id = uniqid();
				if ( isset( $_GET['source_id'] ) ) {
					$source_id = sanitize_text_field( $_GET['source_id'] );
				}
				$form = new SourceForm( $form_url );
				$form->form( $this->sourceService->get_sources( false ), $source_id );
				break;
			case 'delete':
				$source_id = uniqid();
				if ( isset( $_GET['source_id'] ) ) {
					$source_id = sanitize_text_field( $_GET['source_id'] );
				}
				$form = new SourceForm( $form_url );
				$form->delete( $this->sourceService->get_sources( false ), $source_id );
				break;
		}
	}

	/**
	 * Dashboard.
	 *
	 * @since 0.0.7
	 *
	 * @return void
	 */
	public function dashboard(): void {
		$settings = $this->sourceService->get_sources();
		$view = new SourceView( '' );
		$view->dashboard( $settings );
	}

	/**
	 * Source form callback function.
	 *
	 * @since 0.0.7
	 * @param array $form_data The form data submitted.
	 *
	 * @return void
	 */
	public function save( array $form_data ): void {
		$stored_sources = $this->sourceService->get_sources( false );
		$should_save = true;
		$source_id = null;
		$source_data = array();
		if ( isset( $form_data['source_id'] ) ) {
			$source_id = sanitize_text_field( $form_data['source_id'] );
		} else {
			$this->noticeManager->add_notice( 'Source id is not set, please try again.', 'error' );
			$should_save = false;
		}
		if ( $source_id && isset( $stored_sources[ $source_id ] ) ) {
			$source_data = $stored_sources[ $source_id ];
		}

		$enabled = false;
		if ( isset( $form_data['enabled'] ) ) {
			$enabled = $form_data['enabled'];
		}

		if ( isset( $form_data['source_type'] ) ) {
			$source_type = sanitize_text_field( $form_data['source_type'] );
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
					if ( ! empty( $plugin_data ) && isset( $plugin_data['Name'] ) ) {
						$source_name = $plugin_data['Name'];
					} else {
						$should_save = false;
					}
					break;
			}

			$source_data = array(
				'source_type' => $source_type,
				'source_path' => $source_path,
				'source_id' => $source_id,
				'enabled' => $enabled,
				'source_name' => $source_name,
			);
		} else {
			$should_save = false;
		}

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

		$stored_sources[ $source_id ] = $source_data;

		if ( $should_save ) {
			// If this is an existing source, and was previously enabled, disable any components.
			if ( ! $enabled ) {
				do_action( 'acf_component_manager_deactivate_component_source', $source_id );
			}

			$this->sourceService->set_sources( $stored_sources );
		}
	}

	/**
	 * Delete source callback function.
	 *
	 * @since 0.0.7
	 * @param array $form_data The source delete form fields.
	 */
	public function delete( array $form_data ) {

		$stored_sources = $this->sourceService->get_sources( false );
		if ( isset( $form_data['source_id'] ) ) {
			$source_id = sanitize_text_field( $form_data['source_id'] );
		}
		if ( isset( $stored_sources[ $source_id ] ) ) {
			unset( $stored_sources[ $source_id ] );
			do_action( 'acf_component_manager_deactivate_component_source', $source_id );
		}
		$this->sourceService->set_sources( $stored_sources );
	}
}
