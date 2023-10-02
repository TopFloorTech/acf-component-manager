<?php

namespace AcfComponentManager\Form;

// If called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains components form.
 */
class ComponentForm extends FormBase {

	/**
	 * Provides the ComponentForm form.
	 *
	 * @param array $components
	 *   An array of theme components.
	 */
	public function form( array $components ) {
		?>
		<form method="post" action="<?php print $this->get_form_url(); ?>">
			<?php wp_nonce_field( 'acf_component_manager', 'save' ); ?>
			<input type="hidden" name="action" value="save">
			<input type="hidden" name="callback" value="manage_components">
			<div class="instructions">
				<p>ACF Component Manager helps manage ACF components, allowing you to select from existing versions of a component.</p>
				<p class="instructions">To manage components with Component Manager, export the ACF component and place the JSON file in your theme\'s <code>/components/{component_name}/assets</code> directory.</p>
			</div>

			<?php if ( ! empty( $components ) ) : ?>
			<table class="form-table">
			<?php foreach( $components as $component => $component_properties ) : ?>
				<tr class="form-field form-required">
					<th class="row">
						<label for="<?php print $component_properties['hash']; ?>">
							<?php print $component_properties['name']; ?>
						</label>
					</th>
					<?php
						if ( isset( $component_properties['files'] ) ) {
							if ( count( $component_properties['files'] ) > 1 ) {
								?>
								<td>
									<select
										name="file[<?php print $component_properties['hash']; ?>]"
										id="<?php print $component_properties['hash']; ?>-file"
										>
										<option value="">-- Select --</option>
										<?php foreach ( $component_properties['files'] as $file ) : ?>
											<option
												value="<?php print $file; ?>"
												<?php if ( isset( $component_properties['stored']['file'] ) && $component_properties['stored']['file'] == $file ) : ?>
													selected
												<?php endif; ?>
											>
												<?php print $file; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<input
										type="checkbox"
										name="enabled[<?php print $component_properties['hash']; ?>]"
										id="<?php print $component_properties['hash']; ?>-enabled"
										value="1"
										<?php isset( $component_properties['stored']['enabled'] ) ? checked( $component_properties['stored']['enabled'], 1 ) : print ''; ?>
									>
									<label for="<?php print $component_properties['hash']; ?>-enabled">
										<?php _e( 'Enabled', 'acf-component-manager' ); ?>
									</label>
								</td>
								<?php
							}
							elseif ( count( $component_properties['files'] ) == 1 ) {
								?>
								<td>
								<input
									type="hidden"
									name="file[<?php print $component_properties['hash']; ?>]"
									value="<?php print reset( $component_properties['files'] ); ?>">

								<?php print reset( $component_properties['files'] ); ?>
								</td>
								<td>
									<input
										type="checkbox"
										name="enabled[<?php print $component_properties['hash']; ?>]"
										id="<?php print $component_properties['hash']; ?>-enabled"
										value="1"
										<?php isset( $component_properties['stored']['enabled'] ) ? checked( $component_properties['stored']['enabled'] ) : print ''; ?>

									>
									<label for="<?php print $component_properties['hash']; ?>-enabled">
										<?php _e( 'Enabled', 'acf-component-manager' ); ?>
									</label>
								</td>
							<?php
							}
							else {
								?>
								<td>
								<?php print __( 'No files found.  Export the ACF component and place in your theme\'s /components/{component_name}/assets directory to manage.', 'acf-component-manager' ); ?>
								</td>

								<?php
							}
						}
						else {
							?>
							<td>
								<?php print __( 'No files found.  Export the ACF component and place in your theme\'s /components/{component_name}/assets directory to manage.', 'acf-component-manager' ); ?>
							</td>
							<td></td>
							<?php
						}
					?>
				</tr>
			<?php endforeach; ?>
			</table>
				<p class="submit">
					<?php submit_button( __( 'Save Components', 'acf-component-manager' ), 'primary', 'submit' ); ?>
					<input type="hidden" name="acf_component_manager_submit" value="1">
				</p>
			<?php else : ?>
			<div class="warning"><?php _e( 'No components found.', 'topfloor-parcel' ); ?></div>
			<?php endif; ?>

		</form>
		<?php
	}
}
