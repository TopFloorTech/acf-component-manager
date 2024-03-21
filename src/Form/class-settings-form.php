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
class SettingsForm extends FormBase {

	/**
	 * Provides the SettingsForm form.
	 *
	 * @param array $settings The settings.
	 */
	public function form( array $settings ) {
		?>
		<form method="post" action="<?php print $this->get_form_url(); ?>" id="acf-component-manager--settings-form">
			<?php wp_nonce_field( 'acf_component_manager', 'save' ); ?>
			<input type="hidden" name="action" value="save">
			<input type="hidden" name="callback" value="manage_settings">
			<table class="form-table">
				<tr class="form-field form-required">
					<th class="row">
						<label for="dev_mode">
							<?php print __( 'Dev mode', 'acf-component-manager' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox"
							name="dev_mode"
							id="dev_mode"
							value="1"
							<?php checked( $settings['dev_mode'] ); ?>
						>
						<label for="dev_mode">
							<?php print __( 'Enabled', 'acf-component-manager' ); ?>
						</label>
						<p class="helper"><em><?php print __( 'Dev mode enables ACF admin and saving components.', 'acf-component-manager' ); ?></em></p>
					</td>
				</tr>

				<tr class="form-field">
					<th class="row">
						<label form="components_directory">
							<?php print __( 'Components directory', 'acf-component-manager' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							name="components_directory"
							id="components_directory"
							placeholder="components"
							value="<?php print $settings['components_directory'] ?? ''; ?>"
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
							value="<?php print $settings['file_directory'] ?? ''; ?>"
							>
						<p class="helper"><?php print __( 'Directory containing the ACF JSON file, relative to the component. Should not contain leading or trailing slashes \'/\'.', 'acf-component-manager' ); ?></p>
					</td>
				</tr>


			</table>
			<p class="submit">
				<?php submit_button( __( 'Save setting', 'acf-component-manager' ), 'primary', 'submit' ); ?>
				<input type="hidden" name="acf_component_manager_submit" value="1">
			</p>
		</form>
		<?php
	}
}
