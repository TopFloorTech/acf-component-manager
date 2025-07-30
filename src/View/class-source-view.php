<?php
/**
 * Provides Source view.
 *
 * @package acf-component-manager
 * @since 0.0.7
 */

namespace AcfComponentManager\View;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains SourceView.
 *
 * @since 0.0.7
 */
class SourceView extends ViewBase {

	/**
	 * The Sources view.
	 *
	 * @param array $sources The settings array.
	 */
	public function view( array $sources ) {

		$this->update_action( 'add' );
		?>
		<a href="<?php print $this->get_form_url(); ?>" class="button"><?php print __( 'Add source', 'acf-component-manager' ); ?></a>
		<table class="widefat">
			<thead>
			<tr>
				<th>
					<h3><?php print __( 'Source', 'acf-component-manager' ); ?></h3>
				</th>
				<th>
					<h3><?php print __( 'Components directory', 'acf-component-manager' ); ?></h3>
				</th>
				<th>
					<h3><?php print __( 'File directory', 'acf-component-manager' ); ?></h3>
				</th>
				<th>
					<h3><?php print __( 'Enabled', 'acf-component-manager' ); ?></h3>
				</th>
				<th>
					<h3><?php print __( 'Operations', 'acf-component-manager' ); ?></h3>
				</th>

			</tr>
			</thead>
			<tbody>
			<?php
			if ( ! empty( $sources ) ) {
				foreach ( $sources as $source_id => $source ) {
					?>
					<tr>
						<td>
							<?php print $source['source_name']; ?>
						</td>
						<td>
							<?php print $source['components_directory'] ?? '""'; ?>
						</td>
						<td>
							<?php print $source['file_directory'] ?? '""'; ?>
						</td>
						<td>
							<?php print isset( $source['enabled'] ) && $source['enabled'] ? __( 'Yes', 'acf-component-manager' ) : __( 'No', 'acf-component-manager' ); ?>
						</td>
						<td>
							<a href="?page=acf-component-manager&tab=manage_sources&action=edit&source_id=<?php print $source_id; ?>" class="button button-primary"><?php print __( 'Edit', 'acf-component-manager' ); ?></a>
							<a href="?page=acf-component-manager&tab=manage_sources&action=delete&source_id=<?php print $source_id; ?>" class="button button-secondary"><?php print __( 'Delete', 'acf-component-manager' ); ?></a>
						</td>
					</tr>

					<?php
				}
			}
			else {
				?>
				<tr>
					<td colspan="6">
						<?php print __( 'No sources found', 'acf-component-manager' ); ?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>

		<?php
	}

	/**
	 * Dashboard display.
	 *
	 * @since 0.0.7
	 * @param array $sources  The sources.
	 */
	public function dashboard( array $sources ) {
		?>
		<h3><?php print __( 'Enabled sources', 'acf-component-manager' ); ?></h3>
		<table class="widefat">
			<tbody>
			<?php
			if ( ! empty( $sources ) ) {
				foreach ( $sources as $source_id => $source ) {
					?>
					<tr>
						<td class="row-title">
							<?php print $source['source_name']; ?>
						</td>
					</tr>
					<?php
				}
			}
			?>
			</tbody>
		</table>
		<?php
	}
}
