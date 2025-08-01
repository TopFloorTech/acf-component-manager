<?php
/**
 * Contains Component Manager class.
 *
 * @package acf-component-manager
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use AcfComponentManager\Form\ComponentForm;
use AcfComponentManager\Form\ComponentsExportForm;
use AcfComponentManager\Service\SourceService;
use AcfComponentManager\View\ComponentView;
use AcfComponentManager\NoticeManager;

/**
 * Provides ComponentManager class.
 */
class ComponentManager {

	/**
	 * AcfComponentManager\NoticeManager definition.
	 *
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
	 * File pattern.
	 * "$settings['active_theme_directory']}/{$settings['components_directory']}{$component['path']}/{$settings['file_directory']}/"
	 *
	 * @var string
	 */
	protected $file_pattern = '%1$s/%2$s/%3$s/%4$s/';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Load dependencies.
	 */
	protected function load_dependencies() {
		$this->noticeManager = new NoticeManager();
		$this->sourceService = new SourceService();
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
		print '<h2>' . __( 'Manage Components', 'acf-component-manager' ) . '</h2>';

		switch ( $action ) {
			case 'view':
				$view = new ComponentView( $form_url );
				$discovered_components = $this->get_discovered_components();
				$stored_components = $this->get_stored_components();
				$new_components = array();
				if ( ! empty( $discovered_components ) ) {

					// Filters for performance.
					$new_components = array_filter(
						$discovered_components,
						function ( $discovered_component ) use ( $stored_components ) {
							return ! in_array( $discovered_component['hash'], array_column( $stored_components, 'hash' ) );
						}
					);
				}
				$missing_components = $this->get_missing_components( $this->get_stored_components() );

				$view->view( $stored_components, $new_components, $missing_components );
				break;

			case 'edit':
				$form = new ComponentForm( $form_url );
				$discovered_components = $this->get_discovered_components();
				$form_components = array();
				if ( ! empty( $discovered_components ) ) {
					foreach ( $discovered_components as $discovered_component ) {
						$files = $this->get_acf_files( $discovered_component );
						if ( ! empty( $files ) ) {
							$discovered_component['files'] = $files;
						}

						$discovered_component['stored'] = $this->get_stored_component( $discovered_component['hash'] );
						$form_components[ $discovered_component['hash'] ] = $discovered_component;
					}
				}
				$form->form( $form_components );
				break;
		}
	}

	/**
	 * Dashboard.
	 */
	public function dashboard() {
		$enabled_components = $this->get_enabled_components();
		$view = new ComponentView( '' );
		$view->dashboard( $enabled_components );
	}

	/**
	 * Tools.
	 *
	 * @param string $action    The current action.
	 * @param string $form_url  The form URL.
	 */
	public function tools( string $action, string $form_url ) {
		$export_form = new ComponentsExportForm( $form_url );
		$export_form->form();
	}

	/**
	 * Add menu tab.
	 *
	 * @param array $tabs Existing tabs.
	 *
	 * @return array The tabs.
	 */
	public function add_menu_tab( array $tabs ): array {
		$tabs['manage_components'] = __( 'Manage components', 'acf-component-manager' );
		return $tabs;
	}

	/**
	 * Save form data.
	 *
	 * @since 0.0.1
	 *
	 * @param array $form_data The form data array.
	 */
	public function save( array $form_data ) {
		$discovered_components = $this->get_discovered_components();
		$save_components = array();
		if ( ! empty( $discovered_components ) ) {
			foreach ( $discovered_components as $component_properties ) {
				$hash = $component_properties['hash'];
				if ( ! isset( $form_data['file'][ $hash ] ) || ! isset( $form_data['key'][ $hash ] ) ) {
					continue;
				}
				$save_components[ $hash ] = $component_properties;
				$save_components[ $hash ]['file'] = $form_data['file'][ $hash ];
				$save_components[ $hash ]['key'] = $form_data['key'][ $hash ];
				$save_components[ $hash ]['source_id'] = $form_data['source_id'][ $hash ];
				$save_components[ $hash ]['source_name'] = $form_data['source_name'][ $hash ];
				$save_components[ $hash ]['path'] = $form_data['path'][ $hash ];

				if ( isset( $form_data['enabled'][ $hash ] ) ) {
					$save_components[ $hash ]['enabled'] = $form_data['enabled'][ $hash ];
				} else {
					$save_components[ $hash ]['enabled'] = false;
				}
			}
			$this->set_stored_components( $save_components );
		}
	}

	/**
	 * Export Components.
	 *
	 * @since 0.0.1
	 * @param array $export_options An array of export options.
	 *
	 * @see \AcfComponentManager\Admin::export().
	 */
	public function export( array $export_options ) {
		$components = $this->get_stored_components();

		header( 'Content-Type: application/json' );
		header( 'Content-Disposition: attachment; filename=managed-components.json' );
		header( 'Pragma: no-cache' );
		print json_encode( $components );
		exit;
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
	 * Get missing components.
	 *
	 * @since 0.0.1
	 *
	 * @param array $managed_components The components currently managed.
	 *
	 * @return array Array of components that only exist in the database.
	 */
	public function get_missing_components( array $managed_components ) {

		$database_components = $this->get_acf_posts();
		if ( empty( $managed_components ) ) {
			return $database_components;
		}

		return array_filter(
			$database_components,
			function ( $item ) use ( $managed_components ) {
				return ! in_array( $item['key'], array_column( $managed_components, 'key' ) );
			}
		);
	}

	/**
	 * Get theme components.
	 *
	 * @since 0.0.1
	 * @deprecated 0.0.7 Use get_discovered_components().
	 *
	 * @return array
	 *   An array of eligible theme components.
	 */
	public function get_theme_components(): array {
		$components = array();

		$settings = $this->get_settings();

		if ( ! isset( $settings['active_theme_directory'] ) ) {
			return $components;
		}

		$path_parts = array(
			$settings['active_theme_directory'],
			$settings['components_directory'],
		);
		$path_parts = implode( '/', $path_parts );

		foreach ( glob( "{$path_parts}/*/functions.php" ) as $functions_file ) {
			$component = get_file_data( $functions_file, array( 'Component' => 'Component' ) );
			// Get all eligible components.  Components should be in the components
			// theme directory and include the File Header 'Component'.
			if ( ! empty( $component['Component'] ) ) {
				$component_path = str_replace( $path_parts . '/', '', $functions_file );
				$component_path = str_replace( '/functions.php', '', $component_path );

				$components[] = array(
					'name' => $component['Component'],
					'path' => $component_path,
					'hash' => wp_hash( $component_path, '' ),
				);
			}
		}

		return $components;
	}

	/**
	 * Discover components.
	 *
	 * Discovers components in the file system based on 'sources'.
	 *
	 * @since 0.0.7
	 *
	 * @return array The discovered components.
	 */
	public function get_discovered_components(): array {
		$components = array();
		$sources = $this->sourceService->get_sources();
		if ( empty( $sources ) ) {
			return $components;
		}
		foreach ( $sources as $source ) {
			$path_parts = [
				$source['source_path'],
				$source['components_directory'],
			];

			$path_parts = implode( '/', $path_parts );

			foreach ( glob( "{$path_parts}/*/functions.php" ) as $functions_file ) {
				$component = get_file_data( $functions_file, array( 'Component' => 'Component' ) );
				// Get all eligible components.  Components should be in the designated
				// directory and include the File Header 'Component'.
				if ( ! empty( $component['Component'] ) ) {
					$component_path = str_replace( '/functions.php', '', $functions_file );

					$components[] = array(
						'source_id' => $source['source_id'],
						'source_name' => $source['source_name'],
						'name' => $component['Component'],
						'path' => $component_path,
						'hash' => wp_hash( $component_path, '' ),
					);
				}
			}
		}
		 return $components;
	}

	/**
	 * Get ACF json files from components.
	 *
	 * @since 0.0.7
	 * @param array $component The ACF component.
	 *
	 * @return array An array of discovered ACF files.
	 */
	public function get_acf_files( array $component ): array {
		$acf_files = array();

		$path_pattern = $this->get_component_acf_file_path( $component );
		foreach ( glob( "{$path_pattern}*.json" ) as $files ) {

			$loaded_file = file_get_contents( $files );
			if ( $loaded_file ) {

				$json = json_decode( $loaded_file, true );

				// Synced theme components have a different structure.
				$key = $this->get_key_from_json( $json );
				if ( ! $key ) {
					$key = $this->get_key_from_json( reset( $json ) );
				}

				if ( $key ) {
					$file_name = str_replace( $path_pattern, '', $files );
					$acf_files[] = array(
						'file_name' => $file_name,
						'path' => $component['path'],
						'key' => $key,
					);
				}
			}
		}
		return $acf_files;
	}

	/**
	 * Get ACF json files from components.
	 *
	 * @param array $component The ACF theme component.
	 *
	 * @return array
	 *   An array of discovered ACF files.
	 */
	public function get_theme_acf_files( array $component ): array {
		$acf_files = array();

		$path_pattern = $this->get_component_acf_file_path( $component );

		foreach ( glob( "{$path_pattern}*.json" ) as $files ) {

			$loaded_file = file_get_contents( $files );
			if ( $loaded_file ) {

				$json = json_decode( $loaded_file, true );

				// Synced theme components have a different structure.
				$key = $this->get_key_from_json( $json );
				if ( ! $key ) {
					$key = $this->get_key_from_json( reset( $json ) );
				}

				if ( $key ) {
					$file_name = str_replace( $path_pattern, '', $files );
					$acf_files[] = array(
						'file_name' => $file_name,
						'path' => $component['path'],
						'key' => $key,
					);
				}
			}
		}
		return $acf_files;
	}

	/**
	 * Set stored components.
	 *
	 * @since 0.0.1
	 *
	 * @param array $components The components to store.
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
	public function get_stored_components(): array {
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
	 * @param string $component_hash The component hash.
	 *
	 * @return array
	 *   The stored component.
	 */
	public function get_stored_component( string $component_hash ): array {
		$stored_component = array();

		$stored_components = $this->get_stored_components();
		if ( ! empty( $stored_components ) ) {
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
	public function get_enabled_components(): array {
		$enabled_components = array();

		$stored_components = $this->get_stored_components();
		if ( ! empty( $stored_components ) ) {
			foreach ( $stored_components as $hash => $stored ) {
				if ( isset( $stored['enabled'] ) && $stored['enabled'] ) {
					$enabled_components[ $hash ] = $stored;
				}
			}
		}

		return $enabled_components;
	}

	/**
	 * Get ACF posts.
	 *
	 * Aggregates all ACF posts.
	 *
	 * @since 0.0.4
	 *
	 * @return array
	 *   An array of ACF post (field groups, post types, taxonomies, option page)
	 */
	public function get_acf_posts(): array {
		$acf_field_groups = $this->get_acf_field_groups();
		$acf_post_types = $this->get_acf_post_types();
		$acf_taxonomies = $this->get_acf_taxonomies();
		$acf_option_pages = $this->get_acf_option_pages();
		return array_merge( $acf_field_groups, $acf_post_types, $acf_taxonomies, $acf_option_pages );
	}

	/**
	 * Get ACF field groups.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 *   An array of ACF field groups.
	 */
	public function get_acf_field_groups(): array {
		$acf_field_groups = array();
		$args = array(
			'post_type' => 'acf-field-group',
			'posts_per_page' => -1,
		);
		$field_group_query = new \WP_Query( $args );
		if ( $field_group_query->have_posts() ) {
			$field_group_posts = $field_group_query->get_posts();
			foreach ( $field_group_posts as $field_group_post ) {
				$acf_field_groups[] = array(
					'id' => $field_group_post->ID,
					'key' => $field_group_post->post_name,
					'status' => $field_group_post->post_status,
					'name' => $field_group_post->post_title,
				);
			}
		}
		return $acf_field_groups;
	}

	/**
	 * Get ACF post types.
	 *
	 * @since 0.0.4
	 *
	 * @return array
	 *   An array of ACF post types.
	 */
	public function get_acf_post_types(): array {
		$acf_post_types = array();
		$args = array(
			'post_type' => 'acf-post-type',
			'posts_per_page' => -1,
		);
		$post_type_query = new \WP_Query( $args );
		if ( $post_type_query->have_posts() ) {
			$acf_post_type_posts = $post_type_query->get_posts();
			foreach ( $acf_post_type_posts as $acf_post_type ) {
				$acf_post_types[] = array(
					'id' => $acf_post_type->ID,
					'key' => $acf_post_type->post_name,
					'status' => $acf_post_type->post_status,
					'name' => $acf_post_type->post_title,
				);
			}
		}
		return $acf_post_types;
	}

	/**
	 * Get ACF taxonomies.
	 *
	 * @since 0.0.4
	 *
	 * @return array
	 *   An array of ACF taxonomies.
	 */
	public function get_acf_taxonomies(): array {
		$acf_taxonomies = array();
		$args = array(
			'post_type' => 'acf-taxonomy',
			'posts_per_page' => -1,
		);
		$taxonomy_query = new \WP_Query( $args );
		if ( $taxonomy_query->have_posts() ) {
			$acf_taxonomy_posts = $taxonomy_query->get_posts();
			foreach ( $acf_taxonomy_posts as $acf_taxonomy ) {
				$acf_taxonomies[] = array(
					'id' => $acf_taxonomy->ID,
					'key' => $acf_taxonomy->post_name,
					'status' => $acf_taxonomy->post_status,
					'name' => $acf_taxonomy->post_title,
				);
			}
		}
		return $acf_taxonomies;
	}

	/**
	 * Get ACF option pages.
	 *
	 * @since 0.0.4
	 *
	 * @return array
	 *   An array of ACF option pages.
	 */
	public function get_acf_option_pages(): array {
		$acf_option_pages = array();
		$args = array(
			'post_type' => 'acf-ui-options-page',
			'posts_per_page' => -1,
		);
		$option_page_query = new \WP_Query( $args );
		if ( $option_page_query->have_posts() ) {
			$acf_option_page_posts = $option_page_query->get_posts();
			foreach ( $acf_option_page_posts as $acf_option_page ) {
				$acf_option_pages[] = array(
					'id' => $acf_option_page->ID,
					'key' => $acf_option_page->post_name,
					'status' => $acf_option_page->post_status,
					'name' => $acf_option_page->post_title,
				);
			}
		}
		return $acf_option_pages;
	}

	/**
	 * Load components.
	 *
	 * @since 0.0.1
	 */
	public function load_components() {
		$components = $this->get_stored_components();

		if ( ! $this->is_dev_mode() ) {
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

				$path_pattern = $this->get_component_acf_file_path( $component );
				$file_path = $path_pattern . $component['file'];

				try {
					$file = file_get_contents( $file_path );
					if ( $file ) {
						$definition = json_decode( $file, true );
						acf_add_local_field_group( reset( $definition ) );
					}
				} catch ( \Exception $e ) {
					NoticeManager::add_notice( $e->getMessage() );
				}
			}
		}
	}

	/**
	 * Get post by key.
	 *
	 * @param string $post_type The ACF post type.
	 * @param string $key       The ACF key.
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
	 * Get key from JSON.
	 *
	 * @param array $json The JSON array.
	 *
	 * @return string|false
	 *   The key.
	 */
	protected function get_key_from_json( array $json ) {
		if ( isset( $json['key'] ) ) {
			return $json['key'];
		}
		return false;
	}

	/**
	 * Map group properties to post.
	 *
	 * @param array $component The component array.
	 *
	 * @return array The post array.
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
			if ( true == $component['active'] ) {
				$group_properties['post_status'] = 'publish';
			} else {
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
	 * @param array $paths  The ACF JSON save paths.
	 * @param mixed $post   The ACF post.
	 *
	 * @return array
	 *   The altered paths.
	 *
	 * @see acf/json/save_paths
	 */
	public function filter_save_paths( array $paths, $post ) {

		$acf_post = get_post( $post['ID'] );
		if ( ! $acf_post ) {
			return $paths;
		}
		$post_name = $acf_post->post_name;
		$post_type = $acf_post->post_type;

		if ( ! in_array( $post_type, array( 'acf-field-group', 'acf-taxonomy', 'acf-post-type', 'acf-ui-options-page' ), true ) ) {
			return $paths;
		}

		if ( $this->is_dev_mode() ) {
			$enabled_components = $this->get_enabled_components();
			if ( ! empty( $enabled_components ) ) {
				foreach ( $enabled_components as $hash => $component ) {
					$path_pattern = $this->get_component_acf_file_path( $component );
					$file_path = $path_pattern . $component['file'];
					$file = file_get_contents( $file_path );
					if ( $file ) {
						$definition = json_decode( $file, true );

						// Synced theme components have a different structure.
						$key = $this->get_key_from_json( $definition );
						if ( ! $key ) {
							$key = $this->get_key_from_json( reset( $definition ) );
						}
						if ( $key && $key == $post_name ) {
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
	 * @param array $paths The paths.
	 *
	 * @return array
	 *   The paths.
	 *
	 * @see: acf/settings/load_json
	 */
	public function filter_load_paths( array $paths ) {

		$enabled_components = $this->get_enabled_components();

		if ( ! empty( $enabled_components ) ) {
			foreach ( $enabled_components as $component ) {
				$path_pattern = $this->get_component_acf_file_path( $component );
				$paths[] = $path_pattern;
			}
		}
		return $paths;
	}

	/**
	 * Filter save file name.
	 *
	 * @since 0.0.1
	 * @param string $filename  The ACF file name.
	 * @param mixed  $post      The ACF post.
	 * @param string $load_path The ACF load path.
	 *
	 * @return string
	 *   The altered file name.
	 *
	 * @see acf/json/save_file
	 */
	public function filter_save_filename( string $filename, $post, $load_path ) {
		$settings = $this->get_settings();

		$acf_post = get_post( $post['ID'] );
		if ( ! $acf_post ) {
			return $filename;
		}
		$post_name = $acf_post->post_name;
		$post_type = $acf_post->post_type;
		$acf_post_types = array(
			'acf-field-group',
			'acf-post-type',
			'acf-taxonomy',
			'acf-ui-options-page',
		);
		if ( ! in_array( $post_type, $acf_post_types ) ) {
			return $filename;
		}

		$enabled_components = $this->get_enabled_components();
		if ( ! empty( $enabled_components ) ) {
			foreach ( $enabled_components as $hash => $component ) {
				$path_pattern = $this->get_component_acf_file_path( $component );
				$file_path = $path_pattern . $component['file'];
				$file = file_get_contents( $file_path );

				if ( $file ) {
					$definition = json_decode( $file, true );

					// Synced theme components have a different structure.
					$key = $this->get_key_from_json( $definition );
					if ( ! $key ) {
						$key = $this->get_key_from_json( reset( $definition ) );
					}
					if ( $key && $key == $post_name ) {
						$filename = $component['file'];
					}
				}
			}
		}
		return $filename;
	}

	/**
	 * Get component path.
	 *
	 * @since 0.0.1
	 *
	 * @param string $component_path The path to the component.
	 *
	 * @return string|false
	 *   The full path to the component.
	 *
	 * @deprecated
	 */
	protected function get_component_path( string $component_path ) {
		$settings = $this->get_settings();

		if ( isset( $settings['active_theme_directory'] ) ) {
			return sprintf( $this->file_pattern, $settings['active_theme_directory'], $settings['components_directory'], $component_path, $settings['file_directory'] );
		}

		return false;
	}

	/**
	 * Get the Component ACF file path from component.
	 *
	 * @since 0.0.7
	 * @param array $component The component.
	 *
	 * @return string|false The path if it can be determined.
	 */
	protected function get_component_acf_file_path( array $component ): string|false {
		$sources = $this->sourceService->get_sources();

		$sources = array_filter(
			$sources,
			function ( $source ) use ( $component ) {
				return $source['source_id'] === $component['source_id'];
			}
		);

		if ( ! empty( $sources ) ) {
			$source = reset( $sources );
			$component_file_path = $source['file_directory'];
			return trailingslashit( $component['path'] . '/' . $component_file_path );
		}
		return false;
	}

	/**
	 * Deactivate component source.
	 *
	 * @since 0.0.7
	 * @param string $source_id The source id to deactivate.
	 *
	 * @return void
	 */
	public function deactivate_component_source( string $source_id ): void {
		$stored_components = $this->get_stored_components();
		foreach ( $stored_components as $index => $component ) {
			if ( $component['source_id'] === $source_id ) {
				$name = $component['name'];
				$this->noticeManager->add_notice( 'Deactivated component ' . $name );
				unset( $stored_components[ $index ] );
			}
		}
		$this->set_stored_components( $stored_components );
	}

	/**
	 * Checks if dev mode is enabled.
	 *
	 * @return bool
	 *   True if we are in dev_mode.
	 */
	public function is_dev_mode() {
		$settings = $this->get_settings();
		if ( isset( $settings['dev_mode'] ) && $settings['dev_mode'] ) {
			return true;
		}
		return false;
	}
}
