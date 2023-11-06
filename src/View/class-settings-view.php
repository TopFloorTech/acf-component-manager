<?php
/**
 * Provides Settings view.
 *
 * @package acf-component-manager
 */

namespace AcfComponentManager\View;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains SettingsView.
 *
 * @since 0.0.1
 */
class SettingsView extends ViewBase {

	/**
	 * The Settings view.
	 *
	 * @param array $settings The settings array.
	 */
	public function view( array $settings ) {

		$this->update_action( 'edit' );
		?>
		<a href="<?php print $this->get_form_url(); ?>" class="button"><?php print __( 'Edit settings', 'acf-component-manager' ); ?></a>
		<table class="widefat">
			<tbody>
			<tr>
				<td class="row-title">
					<?php print __( 'Dev mode', 'acf-component-manager' ); ?>
				</td>
				<td>
					<?php
					if ( $settings['dev_mode'] ) {
						print __( 'Enabled', 'acf-component-manager' );
					} else {
						print __( 'Disabled', 'acf-component-manager' );
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="row-title">
					<?php print __( 'Components directory', 'acf-component-manager' ); ?>
				</td>
				<td>
					<?php print $settings['components_directory']; ?>
				</td>
			</tr>
			<tr>
				<td class="row-title">
					<?php print __( 'File directory', 'acf-component-manager' ); ?>
				</td>
				<td>
					<?php print $settings['file_directory']; ?>
				</td>
			</tr>
			</tbody>
		</table>

		<?php

	}

	/**
	 * Dashboard display.
	 *
	 * @since 0.0.1
	 * @param array $settings The plugin settings.
	 */
	public function dashboard( array $settings ) {
		?>
		<h3><?php print __( 'Settings', 'acf-component-manager' ); ?></h3>
		<table class="widefat">
			<tbody>
			<tr>
				<td class="row-title">
					<?php print __( 'Dev mode', 'acf-component-manager' ); ?>
				</td>
				<td>
					<?php
					if ( $settings['dev_mode'] ) {
						print __( 'Enabled', 'acf-component-manager' );
					} else {
						print __( 'Disabled', 'acf-component-manager' );
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="row-title">
					<?php print __( 'Components directory', 'acf-component-manager' ); ?>
				</td>
				<td>
					<?php print $settings['components_directory']; ?>
				</td>
			</tr>
			<tr>
				<td class="row-title">
					<?php print __( 'File directory', 'acf-component-manager' ); ?>
				</td>
				<td>
					<?php print $settings['file_directory']; ?>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}
}
