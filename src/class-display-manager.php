<?php
/**
 * @file
 * Provides the display manager.
 */

namespace AcfComponentManager;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class DisplayManager {

	/**
	 * Slug.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var string $slug
	 */
	protected $slug;

	/**
	 * AcfComponentManager\Controller\ComponentManager definition.
	 *
	 * @var \AcfComponentManager\Controller\ComponentManager
	 * @since 0.0.1
	 */
	protected $componentManager;

	/**
	 * AcfComponentManager\Controller\SettingsManager definition.
	 *
	 * @var \AcfComponentManager\Controller\SettingsManager
	 */
	protected $settingsManager;

	/**
	 * Constructs a new DisplayManager object.
	 *
	 * @since 0.0.1
	 * @param string $slug
	 *   The slug.
	 */
	public function __construct( string $slug ) {
		$this->slug = $slug;
		$this->load_dependencies();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 0.0.1
	 */
	private function load_dependencies() {
	}

	/**
	 * Renders a page.
	 *
	 * @since 0.0.1
	 * @param string $current_tab
	 *   The current tab.
	 * @param string $current_action
	 *   The current action.
	 */
	public function render_page( string $current_tab = 'dashboard', string $current_action = 'view' ) {
		$form_url = $this->get_form_url( $current_tab,  $current_action );
		ob_start();
		?>
		<div class="wrap">
			<h1><?php print __( 'ACF Component Manager', 'acf-component-manager' ); ?></h1>
			<?php $this->render_tabs( $current_tab ); ?>
			<?php

			switch ( $current_tab ) {
				case 'dashboard':
					?>
					<h2> <?php print __( 'Dashboard', 'acf-component-manager' ); ?></h2>
					<?php

					break;
				default:
					do_action( "acf_component_manager_render_page_{$current_tab}", $current_action, $form_url );
					break;
			}
			?>
		</div>
		<?php
		$content = ob_get_clean();
		print $content;
	}

	/**
	 * Get tabs
	 *
	 * @since 0.0.1
	 * @param string $current_tab The current tab.
	 */
	public function render_tabs( $current_tab ) {
		$tabs = [
			'dashboard' => __( 'Dashboard', 'acf-component-manager' ),
			//'import' => __( 'Import', 'acf-component-manager' ),
		];

		$tabs = apply_filters( 'acf_component_manager_tabs', $tabs );
		// @todo: Add menu icon.
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $label ) {
			$class = ( $tab == $current_tab ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab ' . $class . '" href="?page=' . $this->slug . '&tab=' . $tab . '">' . $label . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * Get form url.
	 *
	 * @since 0.0.1
	 * @param string $tab
	 *   The tab.
	 * @param string $action
	 *
	 * @return string $form_url
	 */
	public function get_form_url( $tab, $action ) {
		return admin_url( 'admin.php?page=' . $this->slug . '&tab=' . $tab . '&action=' . $action );
	}

}
