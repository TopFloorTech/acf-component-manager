<?php
/**
 * Settings form class.
 *
 * @package acf-component-manager
 */

namespace AcfComponentManager\Form;

/**
 * Contains class for SettingsForm.
 *
 * @since 0.0.1
 */
class SourceForm extends FormBase {

	/**
	 * Provides the SourceForm form.
	 *
	 * @param array  $sources   The settings.
	 * @param string $source_id The source id.
	 */
	public function form( array $sources, string $source_id ) {
		$source = $sources[ $source_id ] ?? array();
		?>
		<form method="post" action="<?php print $this->get_form_url(); ?>">
			<?php wp_nonce_field( 'acf_component_manager', 'save' ); ?>
			<input type="hidden" name="action" value="save">
			<input type="hidden" name="callback" value="manage_sources">
			<input type="hidden" name="source_id" value="<?php print $source_id; ?>">

			<table class="form-table">
				<tr class="form-field form-required">
					<th class="row">
						<label for="source_type"><?php print __( 'Source type', 'acf-component-manager' ); ?></label>
					</th>
					<td>
						<?php
						$active_plugins = get_option( 'active_plugins' );
						?>
						<select name="source_type" required>
							<?php
							$parent_theme_name = wp_get_theme( get_template() )->get( 'Name' );
							$child_theme_name = wp_get_theme( '' )->get( 'Name' );
							if ( $parent_theme_name !== $child_theme_name ) :
								?>
							<option
								value="parent_theme"
								<?php isset( $source['source_type'] ) ? selected( $source['source_type'], 'parent_theme' ) : ''; ?>
							>
								<?php print __( 'Parent theme', 'acf-component-manager' ) . ': ' . $parent_theme_name; ?>
							</option>
							<option
								value="child_theme"
								<?php isset( $source['source_type'] ) ? selected( $source['source_type'], 'child_theme' ) : ''; ?>
							>
								<?php print __( 'Child theme', 'acf-component-manager' ) . ': ' . $child_theme_name; ?>
							</option>
								<?php
							else :
								// If there is only one theme.
								?>
								<option
									value="child_theme"
									<?php isset( $source['source_type'] ) ? selected( $source['source_type'], 'child_theme' ) : ''; ?>
								>
									<?php print __( 'Theme', 'acf-component-manager' ) . ': ' . $child_theme_name; ?>
								</option>
								<?php
							endif;
							if ( ! empty( $active_plugins ) ) {
								foreach ( $active_plugins as $plugin ) {
									$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
									?>
									<option
										value="<?php print $plugin; ?>"
										<?php isset( $source['source_type'] ) ? selected( $source['source_type'], $plugin ) : ''; ?>
									>
										<?php print $plugin_data['Name']; ?>
									</option>
									<?php
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
				<tr class="form-field">
					<th class="row">
						<label for="components_directory">
							<?php print __( 'Components directory', 'acf-component-manager' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							name="components_directory"
							id="components_directory"
							placeholder="components"
							value="<?php print $source['components_directory'] ?? ''; ?>"
						>
						<p class="helper"><?php print __( 'Directory containing components in the active theme.  Should not contain leading or trailing slashes \'/\'.', 'acf-component-manager' ); ?></p>
					</td>
				</tr>

				<tr class="form-field">
					<th class="row">
						<label for="file_directory">
							<?php print __( 'Component ACF file directory', 'acf-component-manager' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							name="file_directory"
							id="file_directory"
							placeholder="assets"
							value="<?php print $source['file_directory'] ?? ''; ?>"
						>
						<p class="helper"><?php print __( 'Directory containing the ACF JSON file, relative to the component. Should not contain leading or trailing slashes \'/\'.', 'acf-component-manager' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th class="row">
						<label for="enabled">
							<?php print __( 'Enabled', 'acf-component-manager' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox"
							name="enabled"
							<?php
							if ( isset( $source['enabled'] ) ) {
								checked( $source['enabled'], 'on' );
							}
							?>
						>
						<?php
							if ( isset( $source['enabled'] ) && 'on' === $source['enabled'] ) {
								print '<p class="helper">' . __( 'Disabling this source will deactivate any components using it.', 'acf-component-manager' ) . '</p>';
							}
 						?>

					</td>
				</tr>

			</table>

			<?php submit_button( __( 'Save source', 'acf-component-manager' ), 'primary', 'submit' ); ?>
			<input type="hidden" name="acf_component_manager_submit" value="1">

		<?php
	}

	/**
	 * Delete source form.
	 *
	 * @since 0.0.7
	 * @param array  $sources   The sources from which to delete.
	 * @param string $source_id The source id to delete.
	 */
	public function delete( array $sources, string $source_id ) {
		$source = $sources[ $source_id ] ?? array();
		?>
			<form method="post" action="<?php print $this->get_form_url(); ?>">
				<?php wp_nonce_field( 'acf_component_manager', 'delete' ); ?>
				<input type="hidden" name="action" value="delete">
				<input type="hidden" name="callback" value="manage_sources">
				<input type="hidden" name="source_id" value="<?php print $source_id; ?>">
				<?php
				print '<h3>' . __( 'Are you sure you want to delete this source?', 'acf-component-manager' ) . '</h3>';
				if ( isset( $source['source_name'] ) ) {
					print '<p><strong>' . $source['source_name'] . '</strong></p>';
				}
				print '<p>' . __( 'All components using this source will be deactivated. This action cannot be undone.', 'acf-component-manager' ) . '</p>';
				?>
				<?php submit_button( __( 'Delete source', 'acf-component-manager' ), 'primary', 'submit' ); ?>
				<input type="hidden" name="acf_component_manager_submit" value="1">

		</form>
		<?php
	}
}
