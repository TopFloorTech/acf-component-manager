( function ( $ ) {
	'use strict';

	// Dismiss notices.
	$( document ).on( 'click', '.acf-component-manager-notice .notice-dismiss', function ( e ) {
		e.preventDefault();
		let notice = $( this ).parent( '.acf-component-manager-notice.is-dismissible' );
		let dismissUrl = notice.data( 'dismiss-url' );
		if ( dismissUrl ) {
			$.get( dismissUrl );
		}
	} );

	$(function() {
		enable_import();
		$("#dev_mode").on("click", enable_import);
	});

	function enable_import() {
		var $dev_mode = $('#dev_mode');
		var $checked = $dev_mode.is(':checked');
		var $import_components = $('#import_components');
		$import_components.prop("disabled", !$checked);
		if (!$checked) {
			$import_components.prop("checked", false);
		}
	}

} ) ( jQuery );
