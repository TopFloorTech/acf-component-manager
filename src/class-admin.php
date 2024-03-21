<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package acf-component-manager
 *
 * @since 0.0.1
 */

namespace AcfComponentManager;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin class.
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Settings.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * Current tab.
	 *
	 * @since 0.0.1
	 * @var string $currentTab
	 *   The currently selected tab.
	 */
	protected $currentTab;

	/**
	 * Current action.
	 *
	 * @since 0.0.1
	 * @var string $currentAction
	 *   The current action.
	 */
	protected $currentAction;

	/**
	 * Slug.
	 *
	 * @since 0.0.1
	 * @var string $slug
	 *   The slug.
	 */
	protected $slug;

	/**
	 * Query string.
	 *
	 * @since 0.0.1
	 * @var string $queryString
	 *   The query string.
	 */
	protected $queryString;

	/**
	 * AcfComponentManager\DisplayManager definition.
	 *
	 * @since 0.0.1
	 * @var \AcfComponentManager\DisplayManager
	 */
	protected $displayManager;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		$this->slug = 'acf-component-manager';
		$this->currentTab = $this->set_current_tab();
		$this->currentAction = $this->set_current_action();
		$this->queryString = $this->set_query_string();
		$this->settings = $this->get_settings();
		$this->load_dependencies();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 0.0.1
	 */
	private function load_dependencies() {
		$this->displayManager = new DisplayManager( $this->slug );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 0.0.1
	 */
	public function enqueue_scripts() {
		wp_register_script( 'acf-component-manager', ACF_COMPONENT_MANAGER_ADMIN_ASSETS . 'js/acf-component-manager.js', array(), ACF_COMPONENT_MANAGER_VERSION, true );
		wp_enqueue_script( 'acf-component-manager' );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 0.0.1
	 */
	public function enqueue_styles() {
		wp_register_style( 'acf-component-manager', ACF_COMPONENT_MANAGER_ADMIN_ASSETS . 'css/acf-component-manager.css', array(), ACF_COMPONENT_MANAGER_VERSION );
		wp_enqueue_style( 'acf-component-manager' );
	}

	/**
	 * Gets admin hooks.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 *   An array of hooks.
	 */
	public function get_hooks() {
		$hooks = array(
			array(
				'hook' => 'admin_menu',
				'callback' => 'add_admin_menu',
			),
		);
		return $hooks;
	}

	/**
	 * Save form data.
	 *
	 * @since 0.0.1
	 */
	public function save() {
		if ( ! wp_verify_nonce( $_REQUEST['save'], 'acf_component_manager' ) ) {
			// Need a notification manager to handle messaging.
			return;
		}

		if ( ! isset( $_REQUEST['callback'] ) ) {
			// Need a notification.
			return;
		}

		do_action( "acf_component_manager_save_{$_REQUEST['callback']}", $_REQUEST );
	}

	/**
	 * Export.
	 *
	 * @since 0.0.1
	 */
	public function export() {
		if ( ! wp_verify_nonce( $_REQUEST['export'], 'acf_component_manager' ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['callback'] ) ) {
			return;
		}

		// Figure out how to export a file generically.
		do_action( "acf_component_manager_export_{$_REQUEST['callback']}", $_REQUEST );
	}

	/**
	 * Delete items.
	 *
	 * @since 2.0.0
	 * @param string $type The component type to delete.
	 */
	public function delete( $type ) {}

	/**
	 * Register admin menu.
	 *
	 * @since 0.0.1
	 */
	public function add_admin_menu() {
		$options_page = add_options_page(
			'ACF Components Manager Settings',
			__( 'ACF Component Manager', 'acf-component-manager' ),
			'manage_options',
			'acf-component-manager',
			array( $this, 'create_admin_interface' )
		);
		add_action( "load-{$options_page}", array( $this, 'router' ) );
	}

	/**
	 * Callback for admin routing.
	 *
	 * @since 0.0.1
	 */
	public function router() {
		if ( isset( $_POST['action'] ) ) {
			switch ( $_POST['action'] ) {
				case 'save':
					$this->save();
					break;
				case 'export':
					$this->export();
					break;
				case 'delete':
					break;
			}
			wp_redirect( admin_url( 'admin.php?page=' . $this->slug . $this->queryString ) );
			exit;
		}
	}

	/**
	 * Create admin interface.
	 * Callback to create the admin interface.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function create_admin_interface() {
		$this->displayManager->render_page( $this->currentTab, $this->currentAction );
	}

	/**
	 * Action Links callback.
	 *
	 * @since 0.0.1
	 *
	 * @param array $links  The array of links.
	 */
	public function action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=acf-component-manager' ) . '">' . __( 'Settings', 'acf-component-manager' ) . '</a>',
			),
			$links
		);
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
		return get_option( SETTINGS_OPTION_NAME );
	}

	/**
	 * Option Manager.
	 * Fires when update_option() is complete.
	 *
	 * @since 0.0.1
	 * @param mixed  $old_value     The value before update.
	 * @param mixed  $new_value     The new value.
	 * @param string $option_name   The option to be updated.
	 */
	public function option_manager( $old_value, $new_value, $option_name ) {
		$settings = $this->get_settings();
	}

	/**
	 * Set the current tab.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 *   The current tab.
	 */
	public function set_current_tab() {
		$tab = 'dashboard';
		if ( isset( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		}
		return $tab;
	}

	/**
	 * Get the current tab.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 *   The current tab.
	 */
	public function get_current_tab() {
		return $this->currentTab;
	}

	/**
	 * Set the current action.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 *   The current action.
	 */
	public function set_current_action() {
		$action = 'view';
		if ( isset( $_GET['action'] ) ) {
			$action = $_GET['action'];
		}
		return $action;
	}

	/**
	 * Get the current action.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 *   The current action.
	 */
	public function get_current_action() {
		return $this->currentAction;
	}

	/**
	 * Set the query string.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 *   The query string.
	 */
	public function set_query_string() {
		$query_string = '&tab=' . $this->currentTab;
		return $query_string;
	}

	/**
	 * Get the query string.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 *   The query string.
	 */
	public function get_query_string() {
		return $this->queryString;
	}
}
