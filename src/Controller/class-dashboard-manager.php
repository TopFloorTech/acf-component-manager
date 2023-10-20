<?php
/**
 * @file
 * Contains Dashboard Manager class.
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class DashboardManager {

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
		print '<h2>' . __('ACF Component Manager Dashboard', 'acf-component_manager') . '</h2>';
		do_action( 'acf_component_manager_dashboard' );
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
		$tabs['dashboard'] = __( 'Dashboard', 'acf-component-manager' );
		return $tabs;
	}

}
