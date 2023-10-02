<?php
/**
 * @file
 * Core plugin class.
 */

namespace AcfComponentManager;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use AcfComponentManager\Admin;
use AcfComponentManager\Loader;
use AcfComponentManager\ComponentManager;

class AcfComponentManager {

	/**
	 * Plugin name
	 *
	 * @since 0.0.1
	 * @var string $plugin_name
	 */
	protected $plugin_name;

	/**
	 * Version
	 *
	 * @since 0.0.1
	 * @var string $version
	 */
	protected $version;

	/**
	 * Plugin file name
	 *
	 * @since 0.0.1
	 * @var string $plugin_file_name
	 */
	protected $plugin_file_name;

	/**
	 * RateCalculator\Admin definition.
	 *
	 * @var \AcfComponentManager\Admin
	 * @since 0.0.1
	 * @access protected
	 */
	protected $admin;

	/**
	 * AcfComponentManager\Loader definition.
	 *
	 * @var \AcfComponentManager\Loader
	 * @since 0.0.1
	 * @access protected
	 */
	protected $loader;

	/**
	 * AcfComponentManager\ComponentManager definition.
	 *
	 * @var \AcfComponentManager\ComponentManager
	 *
	 * @since 0.0.1
	 */
	protected $componentManager;

	/**
	 * Options.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var array $options
	 */
	protected $options;

	/**
	 * Constructs a new Acf_Component_Manager object.
	 */
	public function __construct() {
		if ( defined( 'RATE_CALCULATOR_VERSION' ) ) {
			$this->version = ACF_COMPONENT_MANAGER_VERSION;
		}
		else {
			$this->version = '0.0.1';
		}

		$this->plugin_name = 'acf-component-manager';

		$this->options = $this->get_options();

		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Load Dependencies.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	private function load_dependencies() {

		//$this->admin = new Admin( $this->plugin_name, $this->version );
		$this->admin = new Admin();
		$this->loader = new Loader();
		$this->componentManager = new ComponentManager();

	}

	/**
	 * Get 'Options'.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array $options
	 */
	public function get_options() {

		$options = array();
		return $options;

	}

	/**
	 * Define admin hooks.
	 *
	 * @since 0.0.1
	 * @access private
	 */
	private function define_admin_hooks() {

		$plugin_admin = $this->admin;
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$component_manager = $this->componentManager;
		$this->loader->add_action( 'acf/init', $component_manager, 'load_components' );
		$this->loader->add_action( 'acf_component_manager_render_page_manage_components', $component_manager, 'render_page', 10, 2 );
		$this->loader->add_action( 'acf_component_manager_save_manage_components', $component_manager, 'save', 10, 1 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    \src\Loader    Orchestrates the hooks of the plugin.
	 *@since     0.0.1
	 *
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
