<?php

namespace AcfComponentManager\View;

// If called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains components view.
 */
class ComponentView extends ViewBase {

	/**
	 * Provides the ComponentView view.
	 *
	 * @param array $components
	 *   An array of theme components.
	 */
	public function view( array $components ) {

		if ( ! empty( $components ) ) {
			foreach( $components as $component ) {
				print '<h3>' . $component['name'] . '</h3>';
				print '<p><strong>' . __( 'File name', 'acf-component-manager' ) . ': </strong> ' . $component['file'] . '</p>';
				print '<p><strong>' . __( 'Enabled', 'acf-component-manager' ) . ': </strong>';
				if ( $component['enabled'] ) {
					print 'Yes';
				}
				else {
					print 'No';
				}
				print '</p>';
			}
		}

		$this->update_action( 'edit' );
		?>
		<a href="<?php print $this->get_form_url(); ?>" class="button"><?php print __( 'Edit components', 'acf-component-manager' ); ?></a>
		<?php
	}
}
