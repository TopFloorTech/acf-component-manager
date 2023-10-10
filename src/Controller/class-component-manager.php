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
use AcfComponentManager\NoticeManager;

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
	 * AcfComponentManager\NoticeManager definition.
	 *
	 * @var \AcfComponentManager\NoticeManager
	 */
	protected $noticeManager;

	/**
	 * File pattern.
	 * $settings['active_theme_directory'] . "/{$component['path']}/assets/"
	 *
	 * @var string
	 */
	protected $file_pattern = '%1$s/%2$s/assets/';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		$this->settings = $this->get_settings();
	}

	/**
	 * Load dependencies.
	 */
	protected function load_dependencies() {
		$this->noticeManager = new NoticeManager();
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
					if ( $save_components[$hash]['file'] == '' ) {
						$save_components[$hash]['enabled'] = false;
					}
				}
				else {
					$save_components[$hash]['enabled'] = false;
				}
			}
			$this->set_stored_components( $save_components );
		}
	}

	/**
	 * Get key from file.
	 *
	 * @since 0.0.1
	 * @param
	 */

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
			return $components;
		}

		foreach ( glob( $settings['active_theme_directory'] . "/components/*/functions.php" ) as $functions_file ) {
			$component = get_file_data( $functions_file, array( 'Component' => 'Component' ) );
			// Get all eligible components.  Components should be in the components
			// theme directory and include the File Header 'Component'.
			if ( ! empty( $component['Component'] ) ) {
				$component_path = str_replace( $settings['active_theme_directory'], '', $functions_file );
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

		//$path_pattern = $settings['active_theme_directory'] . "/{$component['path']}/assets/";
		$path_pattern = sprintf( $this->file_pattern, $component['active_theme_directory'], $component['path'] );
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
		update_option( STORED_COMPONENTS_OPTION_NAME, serialize( $components ) );
	}

	/**
	 * Get stored components.
	 *
	 * @return array
	 *   The components array.
	 */
	public function get_stored_components() : array {
		$components = array();

		$stored_components = get_option( STORED_COMPONENTS_OPTION_NAME );

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
	 * Get enabled components.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 *   An array of enabled components.
	 */
	public function get_enabled_components() : array {
		$enabled_components = array();

		$stored_components = $this->get_stored_components();
		if ( ! empty( $stored_components ) ) {
			foreach ( $stored_components as $hash => $stored ) {
				if ( isset( $stored['enabled'] ) && $stored['enabled'] ) {
					$enabled_components[$hash] = $stored;
				}
			}
		}
		return $enabled_components;
	}

	/**
	 * Load components.
	 *
	 * @since 0.0.1
	 */
	public function load_components() {
		$components = $this->get_stored_components();
		$settings = $this->get_settings();

		if ( isset( $settings['dev_mode'] ) && $settings['dev_mode'] != true ) {
			return;
		}

		if ( ! isset( $settings['active_theme_directory'] ) ) {
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

				//$path_pattern = $settings['active_theme_directory'] . "{$component['path']}/assets/";
				$path_pattern = sprintf( $this->file_pattern, $settings['active_theme_directory'], $component['path'] );
				$file_path = $path_pattern . $component['file'];

				try {
					$file = file_get_contents( $file_path );
					if ( $file ) {
						$definition = json_decode( $file, true );
						acf_add_local_field_group( reset( $definition ) );
					}
				}
				catch ( \Exception $e ) {
					print $e->getMessage();
				}
			}
		}
 	}

	/**
	 * Dev mode switch.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $old_value
	 *   The original value.
	 * @param mixed $new_value
	 *   The new value.
	 * @param string $option
	 *   The option name.
	 */
	public function dev_mode_switch( $old_value, $new_value, $option ) {
		NoticeManager::add_notice( 'dev_mode_switch' );
		// If going into dev mode.
		if (
			isset( $old_value['dev_mode'] ) &&
			! $old_value['dev_mode'] &&
			isset( $new_value['dev_mode'] ) &&
			$new_value['dev_mode']
		) {
			$this->dev_mode_activate();
		}
		// If going out of dev mode.
		if (
			isset( $old_value['dev_mode'] ) &&
			$old_value['dev_mode'] &&
			(
				(
					isset( $new_value['dev_mode'] ) &&
					! $new_value['dev_mode']
				)
				||
				! isset( $new_value['dev_mode'] )
			)
		) {
			$this->dev_mode_deactivate();
		}
	}

	/**
	 * Activate dev mode.
	 */
	protected function dev_mode_activate() {
		NoticeManager::add_notice( 'dev_mode_deactivate' );
		$components = $this->get_enabled_components();
		$settings = $this->get_settings();
		if ( !empty( $components ) ) {
			foreach ( $components as $hash => $component ) {
				//$path_pattern = $settings['active_theme_directory'] . "{$component['path']}/assets/";
				$path_pattern = sprintf( $this->file_pattern, $settings['active_theme_directory'], $component['path'] );
				$file_path = $path_pattern .  $component['file'];
				try {
					$file = file_get_contents($file_path);
					if ( $file ) {
						$definition = json_decode($file, TRUE);
						$field_group = array();
						if ( isset( $definition[0] ) ) {
							$field_group = $definition[0];
						}

						if ( empty( $field_group ) ) {
							continue;
						}

						$fields = array();
						if ( isset( $field_group['fields'] ) ) {
							$fields = $field_group['fields'];
							unset( $field_group['fields'] );
						}
						$group_post = false;
						if ( $key = $field_group['key']) {
							// Query posts with matching key.
							$group_post = $this->get_post_by_key( 'acf-field-group', $key );
						}

						$group_properties = $this->map_group_properties_to_post( $field_group );


						if ( ! $group_post ) {
							// Create a new post.
						}

						else {

						}



						continue;
					}
				}
				catch ( \Exception $e ) {
					$message = $e->getMessage();
					die( $message );
				}


			}
		}
	}

	/**
	 * Deactivate dev mode.
	 */
	protected function dev_mode_deactivate() {
		$components = $this->get_enabled_components();
		$settings = $this->get_settings();
		//die( print_r( $components ) );
		NoticeManager::add_notice('dev_mode_deactivate: ' . wp_get_environment_type() );
		if ( ! empty( $components ) ) {
			foreach ( $components as $hash => $component ) {
				//$path_pattern = $settings['active_theme_directory'] . "{$component['path']}/assets/";
				$path_pattern = sprintf( $this->file_pattern, $settings['active_theme_directory'], $component['path'] );
				$file_path = $path_pattern .  $component['file'];
				NoticeManager::add_notice( $file_path );
				try {
					$file = file_get_contents( $file_path );
					if ( $file ) {
						$definition = json_decode($file, TRUE);
						$group_post = false;
						if ( $key = $definition[0]['key']) {
							$group_post = $this->get_post_by_key( 'acf-field-group', $key );
						}

						if ( $group_post ) {

						}
						$field_posts = false;

						continue;
					}
				}
				catch ( \Exception $e ) {
					$message = $e->getMessage();
					die( $message );
				}
			}
		}
	}

	/**
	 * Get post by key.
	 *
	 * @param string $post_type
	 *   The ACF post type.
	 * @param string $key
	 *   The ACF key.
	 *
	 * @return mixed
	 *   The post if found.
	 */
	protected function get_post_by_key( string $post_type, string $key ) {
		$args = array(
			'post_name' => $key,
			'post_type' => $post_type,
		);
		$posts = get_posts( $args );
		if ( $posts ) {
			return reset( $posts );
		}
		return false;
	}

	/**
	 * Map group properties to post.
	 *
	 * @param array $component
	 *   The component array.
	 *
	 * @param array
	 *   The post array.
	 */
	protected function map_group_properties_to_post( array $component ) {
		$group_properties = array();
		if ( ! isset( $component['key'] ) ) {
			return $group_properties;
		}
		$group_properties['key'] = $component['key'];
		unset( $component['key'] );

		if ( ! isset( $component['title'] ) ) {
			return $group_properties;
		}

		$group_properties['title'] = $component['title'];
		$group_properties['post_name'] = sanitize_title( $component['title'] );
		unset( $component['title'] );

		$group_properties['post_type'] = 'acf-field-group';

		if ( isset( $component['menu_order'] ) ) {
			$group_properties['menu_order'] = $component['menu_order'];
			unset( $component['menu_order'] );
		}

		if ( isset( $component['active'] ) ) {
			if ( $component['active'] == true ) {
				$group_properties['post_status'] = 'publish';
			}
			else {
				$group_properties['post_status'] = 'acf-disabled';
			}
			unset( $component['active'] );
		}

		// Fields are mapped to a separate post.
		if ( isset( $component['fields'] ) ) {
			unset( $component['fields'] );
		}

		$group_properties['post_content'] = maybe_serialize( $component );

		return $group_properties;
	}

	/**
	 * Filter save path.
	 *
	 * @since 0.0.1
	 * @param array $paths
	 *   The ACF JSON save paths.
	 * @param mixed $post
	 *   The ACF post.
	 *
	 * @return array
	 *   The altered paths.
	 */
	public function filter_save_paths( array $paths, $post ) {
		$settings = $this->get_settings();
		$post_name = $post->post_name;
		$post_type = $post->post_type;
		if ( $post_type !== 'acf-field-group' ) {
			return $paths;
		}
		if ( isset( $settings['dev_mode'] ) && $settings['dev_mode'] ) {
			$enabled_components = $this->get_enabled_components();
			if ( ! empty( $enabled_components ) ) {
				foreach ( $enabled_components as $hash => $component ) {
					$path_pattern = sprintf( $this->file_pattern, $settings['active_theme_directory'], $component['path'] );
					$file_path = $path_pattern . $component['file'];
					$file = file_get_contents($file_path);
					if ($file) {
						$definition = json_decode($file, TRUE);
						if ( isset( $definition[0]['key'] ) && $definition[0]['key'] == $post_name ) {
							$paths = array( $path_pattern );
						}
					}
				}
			}
		}
		return $paths;
	}

	/**
	 * Filter load paths.
	 *
	 * @since 0.0.1
	 * @param array $paths
	 *   The paths.
	 *
	 * @return array
	 *   The paths.
	 */
	public function filter_load_paths( array $paths ) {
		$settings = $this->get_settings();
		$enabled_components = $this->get_enabled_components();
		if ( ! empty( $enabled_components ) ) {
			foreach ($enabled_components as $hash => $component) {
				$path_pattern = sprintf( $this->file_pattern, $settings['active_theme_directory'], $component['path'] );
				$paths[] = $path_pattern;
			}
		}
		return $paths;
	}

	/**
	 * Filter save file name.
	 *
	 * @since 0.0.1
	 * @param string $filename
	 *   The ACF file name.
	 * @param mixed $post
	 *   The ACF post.
	 * @param string $load_path
	 *   The ACF load path.
	 *
	 * @return string
	 *   The altered file name.
	 */
	public function filter_save_filename( string $filename, $post, $load_path ) {
		$settings = $this->get_settings();
		/*
		if ( ! isset( $settings['dev_mode'] ) || ! $settings['dev_mode'] ) {
			return $filename;
		}
		/**/
		$post_name = $post->post_name;
		$post_type = $post->post_type;
		if ( $post_type !== 'acf-field-group' ) {
			return $filename;
		}

		$enabled_components = $this->get_enabled_components();
		if ( ! empty( $enabled_components ) ) {
			foreach ( $enabled_components as $hash => $component ) {
				$path_pattern = sprintf( $this->file_pattern, $settings['active_theme_directory'], $component['path'] );
				$file_path = $path_pattern . $component['file'];
				$file = file_get_contents( $file_path );
				if ( $file ) {
					$definition = json_decode( $file, TRUE );
					if ( isset( $definition[0]['key'] ) && $definition[0]['key'] == $post_name ) {
						return $component['file'];
					}
				}
			}
		}
		return $filename;
	}


}
