<?php

namespace AcfComponentManager\View;

// If called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Contains a base class for views.
 */
class ViewBase {

	/**
	 * The form url.
	 *
	 * @var string
	 */
	protected $formUrl;

	/**
	 * Constructs a new ViewBase object.
	 *
	 * @param string $form_url
	 *   The form URL.
	 */
	public function __construct( string $form_url ) {
		$this->formUrl = $form_url;
	}

	/**
	 * Getter for formUrl.
	 *
	 * @return string
	 *   The form url.
	 */
	public function get_form_url() {
		return $this->formUrl;
	}

	/**
	 * Setter for formUrl.
	 *
	 * @param string $url
	 *   The URL to set.
	 *
	 * @return void
	 */
	public function set_form_url( string $url ) {
		$this->formUrl = $url;
	}

	/**
	 * Update action.
	 *
	 * @param string $action
	 *   The new action.
	 */
	public function update_action( string $action ) {
		$original_url = $this->get_form_url();
		$query = parse_url( $original_url, PHP_URL_QUERY );
		$query_parts = explode( '&', $query );
		if ( $query_parts ) {
			foreach ( $query_parts as $key => $query_part ) {
				if ( strpos( $query_part, 'action=' ) !== false ) {
					$query_parts[$key] = 'action=' . $action;
				}
			}
		}
		$updated_query = implode( '&', $query_parts );
		$updated_url = str_replace( $query, $updated_query, $original_url );
		$this->set_form_url( $updated_url );
	}

}
