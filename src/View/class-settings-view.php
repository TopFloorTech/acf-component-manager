<?php

namespace AcfComponentManager\View;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains SettingsView.
 */
class SettingsView extends ViewBase {

	/**
	 * The Settings view.
	 *
	 * @param array $settings
	 *   The settings array.
	 */
	public function view( array $settings ) {
		print '<pre>';
		print_r( $settings );
		print '</pre>';
		$this->update_action( 'edit' );
		?>
		<a href="<?php print $this->get_form_url(); ?>" class="button"><?php print __( 'Edit settings', 'acf-component-manager' ); ?></a>
		<?php

	}
}
