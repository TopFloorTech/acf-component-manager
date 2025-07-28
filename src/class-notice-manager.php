<?php
/**
 * Contains Notices class.
 *
 * @package acf-component-manager
 *
 * @since 0.0.1
 */

namespace AcfComponentManager;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to display notices.
 */
class NoticeManager {

	/**
	 * Show notices.
	 *
	 * @since 0.0.1
	 */
	public function show_notices() {
		global $pagenow;
		$notices = $this->get_notices();
		if ( ! empty( $notices ) ) {
			foreach ( $notices as $id => $notice ) {
				if ( ! isset( $notice['scope'] ) ) {
					continue;
				}
				$dismiss_url = '';
				$notice_classes = array(
					'notice',
					'acf-component-manager-notice',
					'notice-' . $notice['type'],
				);
				if ( $notice['dismissible'] ) {
					$dismiss_url = add_query_arg( array( 'acf-component-manager-notice-dismiss' => $id ), admin_url() );
					$notice_classes[] = 'is-dismissible';
				}

				ob_start();
				?>
				<div class="<?php print implode( ' ', $notice_classes ); ?>"
					<?php if ( $notice['dismissible'] ) : ?>
						data-dismiss-url="<?php print esc_url( $dismiss_url ); ?>"
					<?php endif; ?>
					>
					<p><?php _e( $notice['message'] . ' - ' . $notice['hash'], 'acf-component-manager' ); ?></p>
				</div>
				<?php
				$output = ob_get_clean();

				if ( $pagenow == $notice['scope'] ) {
					print $output;
				} elseif ( 'global' == $notice['scope'] ) {
					print $output;
				}
			}
		}
	}

	/**
	 * Add notice.
	 *
	 * @param string $message The message.
	 * @param string $type  One of 'info', 'warning', 'error', 'success'.
	 * @param bool   $dismissible Dismissible or not.
	 * @param string $scope Defines where to display the message.
	 *
	 * @since 0.0.1
	 */
	public static function add_notice( string $message, string $type = 'warning', bool $dismissible = true, string $scope = 'global' ) {
		$notices = get_option( NOTICES_OPTION_NAME, array() );

		$new_notice = array(
			'message' => $message,
			'type' => $type,
			'dismissible' => $dismissible,
			'scope' => $scope,
		);

		// Create a hash so we can reduce duplicates.
		$hash = wp_hash( json_encode( $new_notice ) );

		if ( array_search( $hash, array_column( $notices, 'hash' ), true ) === false ) {
			$new_notice['hash'] = $hash;
			$id = uniqid();
			$notices[ $id ] = $new_notice;
			update_option( NOTICES_OPTION_NAME, $notices );
		}
	}

	/**
	 * Dismiss notice.
	 *
	 * @since 0.0.1
	 */
	public function dismiss_notice() {
		if ( ! isset( $_GET['acf-component-manager-notice-dismiss'] ) ) {
			return;
		}
		$dismiss_notice = htmlspecialchars( $_GET['acf-component-manager-notice-dismiss'] );
		$notices = $this->get_notices();
		if ( isset( $notices[ $dismiss_notice ] ) ) {
			unset( $notices[ $dismiss_notice ] );
			update_option( NOTICES_OPTION_NAME, $notices );
		}
	}

	/**
	 * Get notices.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 *   The stored notices.
	 */
	public function get_notices(): array {
		return get_option( NOTICES_OPTION_NAME, array() );
	}

	/**
	 * Delete all notices.
	 *
	 * @since 0.0.1
	 */
	public function delete_all_notices() {
		update_option( NOTICES_OPTION_NAME, array() );
	}
}
