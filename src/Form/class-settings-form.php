<?php

namespace AcfComponentManager\Form;

/**
 * Contains class for SettingsForm.
 */
class SettingsForm extends FormBase {

	/**
	 * Provides the SettingsForm form.
	 *
	 * @param array $settings
	 *   The settings.
	 */
	public function form( array $settings ) {
		?>
		<form method="post" action="<?php print $this->get_form_url(); ?>">
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
							<?php print __( 'Dev mode', 'acf-component-manager' ); ?>
						</label>
					</td>
			</table>
			<p class="submit">
				<?php submit_button( __( 'Save setting', 'acf-component-manager' ), 'primary', 'submit' ); ?>
				<input type="hidden" name="acf_component_manager_submit" value="1">
			</p>
		</form>
		<?php
	}
}
