<?php
/**
 * Provides a base class for forms.
 *
 * @package acf-component-manager
 */

namespace AcfComponentManager\Form;

// If called directly, short.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Base class for forms.
 *
 * @since 0.0.1
 */
abstract class FormBase {

	/**
	 * The form url.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $formUrl;

	/**
	 * Constructs a new FormBase object.
	 *
	 * @since 0.0.1
	 *
	 * @param string $form_url The form URL.
	 */
	public function __construct( string $form_url ) {
		$this->formUrl = $form_url;
	}

	/**
	 * Get form URL.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 *   The form URL.
	 */
	public function get_form_url() {
		return $this->formUrl;
	}
}
