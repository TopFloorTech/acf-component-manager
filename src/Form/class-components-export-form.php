<?php

namespace AcfComponentManager\Form;

// If called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains components export form.
 */
class ComponentsExportForm extends FormBase {

	/**
	 * Provides the ComponentsExportForm form.
	 */
	public function form() {
		?>
		<form method="post" action="<?php print $this->get_form_url(); ?>">
			<?php wp_nonce_field( 'acf_component_manager', 'export' ); ?>
			<input type="hidden" name="action" value="export">
			<input type="hidden" name="callback" value="manage_components">

				<?php submit_button( __( 'Export managed components', 'acf-component-manager' ), 'primary', 'submit' ); ?>
				<input type="hidden" name="acf_component_manager_submit" value="1">

		</form>
		<?php
	}
}
