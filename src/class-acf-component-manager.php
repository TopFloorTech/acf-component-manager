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

use AcfComponentManager\Controller\ComponentManager;
use AcfComponentManager\Controller\SettingsManager;
use AcfComponentManager\Controller\DashboardManager;
use AcfComponentManager\NoticeManager;

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
	 * AcfComponentManager\Controller\ComponentManager definition.
	 *
	 * @var \AcfComponentManager\Controller\ComponentManager
	 *
	 * @since 0.0.1
	 */
	protected $componentManager;

	/**
	 * AcfComponentManager\Controller\SettingsManager definition.
	 *
	 * @var \AcfComponentManager\Controller\SettingsManager
	 *
	 * @since 0.0.1
	 */
	protected $settingsManager;

	/**
	 * AcfComponentManager\NoticeManager definition.
	 *
	 * @var \AcfComponentManager\NoticeManager
	 */
	protected $noticeManager;

	/**
	 * AcfComponentManager\Controller\DashboardManager definition.
	 *
	 * @var \AcfComponentManager\Controller\DashboardManager
	 */
	protected $dashboardManager;

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
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		if ( defined( 'ACF_COMPONENT_MANAGER_VERSION' ) ) {
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
	 * @access private
	 */
	private function load_dependencies() {

		$this->admin = new Admin();
		$this->loader = new Loader();
		$this->componentManager = new ComponentManager();
		$this->settingsManager = new SettingsManager();
		$this->dashboardManager = new DashboardManager();
		$this->noticeManager = new NoticeManager();
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
		if ( wp_get_environment_type() == 'development' ) {
			$this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
		}
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$component_manager = $this->componentManager;
		$this->loader->add_action( 'acf_component_manager_render_page_manage_components', $component_manager, 'render_page', 10, 2 );
		$this->loader->add_action( 'acf_component_manager_dashboard', $component_manager, 'dashboard', 10 );
		$this->loader->add_action( 'acf_component_manager_save_manage_components', $component_manager, 'save', 10, 1 );
		$this->loader->add_filter(  'acf_component_manager_tabs', $component_manager, 'add_menu_tab', 15 );
		$this->loader->add_filter( 'acf/json/save_file_name', $component_manager, 'filter_save_filename', 10, 3 );
		$this->loader->add_filter( 'acf/json/save_paths', $component_manager, 'filter_save_paths', 10, 2 );
		$this->loader->add_filter( 'acf/settings/load_json', $component_manager, 'filter_load_paths', 10, 1 );

		$dashboard_manager = $this->dashboardManager;
		$this->loader->add_filter( 'acf_component_manager_render_page_dashboard', $dashboard_manager, 'render_page' );
		$this->loader->add_filter(  'acf_component_manager_tabs', $dashboard_manager, 'add_menu_tab', 5 );
		$settings_manager = $this->settingsManager;
		$this->loader->add_action( 'acf_component_manager_render_page_manage_settings', $settings_manager, 'render_page', 10, 2 );
		$this->loader->add_action( 'acf_component_manager_dashboard', $settings_manager, 'dashboard', 5 );
		$this->loader->add_action( 'acf_component_manager_save_manage_settings', $settings_manager, 'save', 10, 1 );
		$this->loader->add_filter( 'acf_component_manager_tabs', $settings_manager, 'add_menu_tab', 10 );

		$notice_manager = $this->noticeManager;
		$this->loader->add_action( 'admin_init', $notice_manager, 'dismiss_notice' );
		$this->loader->add_action( 'admin_notices', $notice_manager, 'show_notices' );


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
	 * @since     0.0.1
	 *
	 * @return   \AcfComponentManager\Loader    Orchestrates the hooks of the plugin.
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
