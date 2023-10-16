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
	 * @param array $stored_components
	 *   An array of theme components.
	 * @param array $new_components
	 *   An array of components not stored.
	 */
	public function view( array $stored_components, array $new_components ) {

		if ( empty( $stored_components ) && empty( $new_components ) ) {
			print '<p>' . __( 'No components found.', 'acf-component-manager' ) . '</p>';
		}

		else {
			$this->update_action( 'edit' );
			?>
			<a href="<?php print $this->get_form_url(); ?>" class="button"><?php print __( 'Edit components', 'acf-component-manager' ); ?></a>
			<?php
		}

		if ( ! empty( $stored_components ) ) {
			print '<h3>' . __( 'Managed theme components', 'acf-component-manager' ) . '</h3>';

			foreach( $stored_components as $component ) {
				print '<h4>' . $component['name'] . '</h4>';
				print '<p><strong>' . __( 'File name', 'acf-component-manager' ) . ': </strong> ' . $component['file'] . '</p>';
				print '<p><strong>' . __( 'Enabled', 'acf-component-manager' ) . ': </strong>';
				if ( $component['enabled'] ) {
					print 'Yes';
				}
				else {
					print 'No';
				}
				print '</p>';
				print '<hr>';
			}
		}

		if ( ! empty( $new_components ) ) {
			print '<h3>' . __( 'Unmanaged theme components', 'acf-component-manager' ) . '</h3>';
			foreach( $new_components as $component ) {
				print '<h4>' . $component['name'] . '</h4>';

				print '<hr>';
			}
		}
	}
}
