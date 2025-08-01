<?php
/**
 * Contains Dashboard Manager class.
 *
 * @package acf-component-manager
 */

namespace AcfComponentManager\Controller;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Manages dashbaod.
 *
 * @since 0.0.1
 */
class DashboardManager {

	/**
	 * Render page.
	 *
	 * @since 0.0.1
	 *
	 * @param string $action   The current action.
	 * @param string $form_url The form URL.
	 */
	public function render_page( string $action = 'view', string $form_url = '' ) {
		print '<h2>' . __( 'ACF Component Manager Dashboard', 'acf-component_manager' ) . '</h2>';
		?>
		<h3><?php print __( 'Basic usage', 'acf-component-manager' ); ?></h3>
		<h4><?php print __( 'Settings', 'acf-component-manager' ); ?>:</h4>
		<ol>
			<li><?php print __( 'Enable "Dev mode".', 'acf-component-manager' ); ?></li>

		</ol>
		<h4><?php print __( 'Add sources', 'acf-component-manager' ); ?></h4>
		<ol>
			<li><?php print __( 'Select the themes and plugins that contain the components.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'Set the Components directory (relative to the theme).', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'Set the ACF file directory.  This is the directory inside the component where the ACF JSON file will live.', 'acf-component-manager' ); ?></li>
		</ol>
		<h4><?php print __( 'Creating a component', 'acf-component-manager' ); ?></h4>
		<ol>
			<li><?php print __( 'Create the ACF field group.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'Export the field group JSON.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'Inside the theme Components directory, create a new component directory.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'Inside the component directory, create the directory for the ACF JSON file.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'Inside the component directory, create a functions.php file. At the top of this file, include "Component: {ComponentName}" in the file docblock comment.  This should match the component directory name.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'In ACF Component Manager - Enable the component.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'In ACF, edit the field group and save.  This will sync the component JSON file so future changes are tracked.', 'acf-component-manager' ); ?></li>
		</ol>
		<h5><?php print __( 'Example component directory.', 'acf-component-manager' ); ?></h5>
		<code class="acf-component-manager-example">

			/wp-content/themes/my-theme/components/<br>
			&nbsp;&nbsp; MyComponent/<br>
			&nbsp;&nbsp; MyComponent/assets/my-component.json<br>
			&nbsp;&nbsp; MyComponent/functions.php<br>
		</code>
		<p><strong><?php print __( 'Note', 'acf-component-manager' ); ?>: </strong><?php print __( 'Ensure the components directory and the JSON file directory match the settings in ACF Component Manager.', 'acf-component-manager' ); ?></p>
		<h5><?php print __( 'Example component functions.php', 'acf-component-manager' ); ?></h5>
		<code class="acf-component-manager-example">
			&lt;?php<br>
			/**<br>
			&nbsp; * Component: MyComponent<br>
			&nbsp; */<br>
		</code>
		<p><strong><?php print __( 'Note', 'acf-component-manager' ); ?>: </strong><?php print __( 'It is important that the component name match the directory.', 'acf-component-manager' ); ?></p>
		<h5><?php print __( 'Deployment', 'acf-component-manager' ); ?></h5>
		<ol>
			<li><?php print __( 'All field groups should be exported.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'In production, all field groups should be deleted from ACF.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'In production, enabled components.', 'acf-component-manager' ); ?></li>
		</ol>
		<h5><?php print __( 'Development', 'acf-component-manager' ); ?></h5>
		<ol>
			<li><?php print __( 'Pull the production database.', 'acf-component-manager' ); ?></li>
			<li><?php print __( 'Enable "Dev mode"', 'acf-component-manager' ); ?></li>
			<li>
				<?php
				printf(
					__( 'Sync components, see <a href="%s" target="_blank">Syncing Changes</a>', 'acf-component-manager' ),
					esc_url( 'https://www.advancedcustomfields.com/resources/local-json/#syncing-changes' ),
				);
				?>
			</li>
		</ol>

		<?php
		do_action( 'acf_component_manager_dashboard' );
	}

	/**
	 * Add menu tab.
	 *
	 * @param array $tabs Existing tabs.
	 *
	 * @retun array
	 *   The tabs.
	 */
	public function add_menu_tab( array $tabs ): array {
		$tabs['dashboard'] = __( 'Dashboard', 'acf-component-manager' );
		return $tabs;
	}
}
