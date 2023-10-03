<?php
/**
 * @file
 * Contains Component Manager class.
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use AcfComponentManager\Form\ComponentForm;
use AcfComponentManager\View\ComponentView;

class ComponentManager {

	/**
	 * Settings.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * Stored components option name.
	 *
	 * @const string
	 */
	const STORED_COMPONENTS_OPTION_NAME = 'acf_component_manager_components';

	/**
	 * Settings option name.
	 *
	 * @const string
	 */
	const SETTINGS_OPTION_NAME = 'acf-component-manager-settings';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		$this->settings = $this->get_settings();
		add_filter( 'acf_component_manager_tabs', array( $this, 'add_menu_tab' ) );
		//add_action( 'acf_component_manager_render_page_manage_components', array( $this, 'render_page' ), 10, 2 );
		//add_action( 'acf_component_manager_save_manage_components', array( $this, 'save' ), 10, 1 );
	}

	/**
	 * Render page.
	 *
	 * @since 0.0.1
	 *
	 * @param string $action
	 *   The current action.
	 * @param string $form_url
	 *   The form URL.
	 */
	public function render_page( string $action = 'view', string $form_url = '' ) {
		print '<h2>' . __( 'Manage Components', 'acf-component_manager' ) . '</h2>';

		switch ( $action ) {
			case 'view':
				$view = new ComponentView( $form_url );
				$theme_components = $this->get_theme_components();
				$mew_components = array();
				if ( ! empty( $theme_components ) ) {
					foreach( $theme_components as $theme_component ) {
						$stored_component = $this->get_stored_component( $theme_component['hash']);
						if ( empty( $stored_component ) ) {
							$mew_components[] = $theme_component;
						}

					}
				}
				$view->view( $this->get_stored_components(), $mew_components );
				break;

			case 'edit':
				$form = new ComponentForm( $form_url );
				$theme_components = $this->get_theme_components();
				$form_components = array();
				if ( ! empty( $theme_components ) ) {
					foreach( $theme_components as $theme_component ) {
						$files = $this->get_theme_acf_files( $theme_component );
						if ( ! empty( $files ) ) {
							$theme_component['files'] = $files;
						}

						$theme_component['stored'] = $this->get_stored_component( $theme_component['hash'] );
						$form_components[$theme_component['hash']] = $theme_component;
					}
				}
				$form->form( $form_components );
				break;
		}

	}

	/**
	 * Add menu tab.
	 *
	 * @param array $tabs
	 *   Existing tabs.
	 *
	 * @retun array
	 *   The tabs.
	 */
	public function add_menu_tab( array $tabs ) : array {
		$tabs['manage_components'] = __( 'Manage components', 'acf-component-manager' );
		return $tabs;
	}

	/**
	 * Save form data.
	 *
	 * @since 0.0.1
	 *
	 * @param array $form_data
	 *   The form data array.
	 */
	public function save( array $form_data ) {
		$theme_components = $this->get_theme_components();
		$save_components = array();
		if ( ! empty( $theme_components ) ) {
			foreach ($theme_components as $component_properties) {
				$hash = $component_properties['hash'];
				$save_components[$hash] = $component_properties;
				if ( isset( $form_data['file'][$hash] ) ) {
					$save_components[$hash]['file'] = $form_data['file'][$hash];
				}
				else {
					$save_components[$hash]['file'] = '';
				}
				if ( isset( $form_data['enabled'][$hash] ) ) {
					$save_components[$hash]['enabled'] = $form_data['enabled'][$hash];
				}
				else {
					$save_components[$hash]['enabled'] = false;
				}
			}
			$this->set_stored_components( $save_components );
		}
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
		$stored_settings = get_option( self::SETTINGS_OPTION_NAME );
		if ( $stored_settings ) {
			$settings = unserialize( $stored_settings );
		}
		return $settings;
	}

	/**
	 * Get theme components.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 *   An array of eligible theme components.
	 */
	public function get_theme_components() {
		$components = array();

		$settings = $this->settings;

		if ( ! isset( $settings['active_theme_directory'] ) ) {
			print 'no $settings[active_theme_directory]';
			return $components;
		}

		foreach ( glob( $settings['active_theme_directory'] . "/components/*/functions.php" ) as $functions ) {
			$component = get_file_data( $functions, array( 'Component' => 'Component' ) );
			// Get all eligible components.  Components should be in the components theme directory and include the File Header 'Component'.
			if ( ! empty( $component['Component'] ) ) {
				$component_path = str_replace( $settings['active_theme_directory'], '', $functions );
				$component_path = str_replace( '/functions.php', '', $component_path );

				$components[] = array(
					'name' => $component['Component'],
					'path' => $component_path,
					'hash' => wp_hash( $component_path ),
				);
			}
		}

		return $components;
	}

	/**
	 * Get ACF json files from components.
	 *
	 * @param array $component
	 *   The ACF theme component.
	 *
	 * @return array
	 *   An array of discovered ACF files.
	 */
	public function get_theme_acf_files( array $component ) {
		$acf_files = array();

		$settings = $this->settings;

		if ( ! isset( $settings['active_theme_directory'] ) ) {
			return $acf_files;
		}

		$path_pattern = $settings['active_theme_directory'] . "/{$component['path']}/assets/";
		foreach ( glob( $path_pattern . "*.json" ) as $files ) {
			$acf_files[] = str_replace( $path_pattern, '', $files );
		}

		return $acf_files;

	}

	/**
	 * Set stored components.
	 *
	 * @since 0.0.1
	 *
	 * @param array $components
	 *   The components to store.
	 */
	public function set_stored_components( array $components ) {
		update_option( self::STORED_COMPONENTS_OPTION_NAME, serialize( $components ) );
	}

	/**
	 * Get stored components.
	 *
	 * @return array
	 *   The components array.
	 */
	public function get_stored_components() : array {
		$components = array();

		$stored_components = get_option( self::STORED_COMPONENTS_OPTION_NAME );

		if ( $stored_components ) {
			$components = unserialize( $stored_components );
		}
		return $components;
	}

	/**
	 * Get stored component.
	 *
	 * @since 0.0.1
	 * @param string $component_hash
	 *   The component hash.
	 *
	 * @return array
	 *   The stored component.
	 */
	public function get_stored_component( string $component_hash ) : array {
		$stored_component = array();

		$stored_components = $this->get_stored_components();
		if ( !empty( $stored_components ) ) {
			foreach ( $stored_components as $hash => $stored ) {
				if ( $hash == $component_hash ) {
					$stored_component = $stored;
				}
			}
		}
		return $stored_component;
	}

	/**
	 * Load components.
	 *
	 * @since 0.0.1
	 */
	public function load_components() {
		$components = $this->get_stored_components();
		$settings = $this->get_settings();

		if ( $settings['dev_mode'] ) {
			return;
		}
		if ( ! empty( $components ) ) {
			foreach ( $components as $component ) {
				if ( ! $component['enabled'] ) {
					continue;
				}
				if ( ! isset( $component['path'] ) ) {
					continue;
				}
				if ( ! isset( $component['file'] ) ) {
					continue;
				}
				$path_pattern = $settings['active_theme_directory'] . "/{$component['path']}/assets/";
				$file_path = $path_pattern .  $component['file'];
				$file = file_get_contents( $file_path );
				if ( $file ) {
					$definition = json_decode( $file, true );
					acf_add_local_field_group( reset( $definition ) );
				}
			}
		}
 	}


}
