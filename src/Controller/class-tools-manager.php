<?php
/**
 * Contains Tools Manager class.
 *
 * @package acf-component-manager
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Manages tools.
 *
 * @since 0.0.1
 */
class ToolsManager {

	/**
	 * Render page.
	 *
	 * @since 0.0.1
	 *
	 * @param string $action   The current action.
	 * @param string $form_url The form URL.
	 */
	public function render_page( string $action = 'view', string $form_url = '' ) {
		print '<h2>' . __( 'Tools', 'acf-component-manager' );

		switch ( $action ) {
			case 'view':
				do_action( 'acf_component_manager_tools', $action, $form_url );
				break;

		}
	}

	/**
	 * Add menu tab.
	 *
	 * @param array $tabs Existing tabs.
	 *
	 * @return array
	 *   The updated tabs.
	 */
	public function add_menu_tab( array $tabs ): array {
		$tabs['tools'] = __( 'Tools', 'acf-component-manager' );
		return $tabs;
	}
}
