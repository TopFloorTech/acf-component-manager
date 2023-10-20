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
	 * @param array $managed_components
	 *   An array of theme components.
	 * @param array $unmanaged_components
	 *   An array of components not managed.
	 * @param array $missing_components
	 *   An array of database components not in code.
	 */
	public function view( array $managed_components, array $unmanaged_components, array $missing_components ) {

		if ( empty( $managed_components ) && empty( $unmanaged_components ) ) {
			print '<p>' . __( 'No theme components found.', 'acf-component-manager' ) . '</p>';
		}

		else {
			$this->update_action( 'edit' );
			?>
			<a href="<?php print $this->get_form_url(); ?>" class="button"><?php print __( 'Edit components', 'acf-component-manager' ); ?></a>
			<?php
		}

		if ( ! empty( $managed_components ) ) {
			print '<h3>' . __( 'Managed theme components', 'acf-component-manager' ) . '</h3>';
			?>
			<table class="widefat">
				<thead>
				<tr>
					<th>
						<?php print __( 'Component', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'File name', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'Field group key', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'Enabled', 'acf-component-manager' ); ?>
					</th>
				</tr>
				</thead>
				<tbody>
			<?php foreach( $managed_components as $component ) : ?>
				<tr>
					<td class="row-title">
						<?php print  $component['name']; ?>
					</td>
					<td>
						<?php print $component['file']; ?>
					</td>
					<td>
						<?php print $component['key']; ?>
					</td>
					<td>
						<?php
						if ( $component['enabled'] ) {
							print 'Yes';
						}
						else {
							print 'No';
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}

		if ( ! empty( $unmanaged_components ) ) {
			print '<h3>' . __( 'Unmanaged theme components', 'acf-component-manager' ) . '</h3>';
			?>
			<table class="widefat">
				<thead>
				<tr>
					<th><?php print __( 'Component name', 'acf-component-manager' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach( $unmanaged_components as $component ) : ?>
					<tr>
						<td class="row-title">
							<?php print $component['name']; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}

		if ( ! empty( $missing_components ) ) {
			print '<h3>' . __( 'Missing components', 'acf-component-manager' ) . '</h3>';
			?>
			<table class="widefat">
				<thead>
				<tr>
					<th>
						<?php print __( 'Component name', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'Field group key', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'Status', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'Post id', 'acf-component-manager' ); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $missing_components as $component ) : ?>
				<tr>
					<td class="row-title">
						<?php print $component['name']; ?>
					</td>
					<td>
						<?php print $component['key']; ?>
					</td>
					<td>
						<?php print $component['status']; ?>
					</td>
					<td>
						<?php print $component['id']; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}
	}

	/**
	 * Dashboard.
	 *
	 * @since 0.0.1
	 * @param array $enabled_components
	 *   The components that are currently enabled.
	 */
	public function dashboard( array $enabled_components ) {
		if ( empty( $enabled_components ) ) {
			print '<p>' . __( 'No enabled components.', 'acf-component-manager' ) . '</p>';
		}
		else {
			print '<h3>' . __( 'Enabled components', 'acf-component-manager' ) . '</h3>';
			?>
			<table class="widefat">
				<thead>
				<tr>
					<th>
						<?php print __( 'Component', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'File name', 'acf-component-manager' ); ?>
					</th>
					<th>
						<?php print __( 'Enabled', 'acf-component-manager' ); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach( $enabled_components as $component ) : ?>
					<tr>
						<td class="row-title">
							<?php if ( isset( $component['name'] ) ) : ?>
							<?php print  $component['name']; ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( isset( $component['file'] ) ) : ?>
							<?php print $component['file']; ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( isset( $component['enabled'] ) ) : ?>
							<?php
							if ( $component['enabled'] ) {
								print 'Yes';
							}
							else {
								print 'No';
							}
							?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}
	}
}
