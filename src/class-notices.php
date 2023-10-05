<?php
/**
 * @file
 * Contains Notices class.
 */

namespace AcfComponentManager;

// If this file is called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to display notices.
 */
class Notices {

	/**
	 * Message string.
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Message type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Dismissible.
	 *
	 * @var bool
	 */
	protected $dismissible;

	/**
	 * Constructs a new Notices object.
	 *
	 * @param string $message
	 *   The message.
	 * @param string $type
	 *   The message type.
	 * @param bool $dismissible
	 *   Dismissible.
	 */
	public function __construct( string $message, string $type = 'warning', bool $dismissible = true ) {
		$this->message = $message;
		$this->type = $type;
		$this->dismissible = $dismissible;

		add_action( 'admin_notices', array( $this, 'render' ) );
	}

	/**
	 * Display a notice.
	 *
	 * @return void
	 */
	public function render() {
		$dismissible = 'is-dismissible';
		if ( ! $this->dismissible ) {
			$dismissible = '';
		}
		//printf( '<div class="notice notice-%s$1 %s$2"><p>%s$3</p>', $this->type, $dismissible, $this->message );
		print 'Test';
	}
}
