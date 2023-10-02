<?php
/**
 * @file
 * Provides autoloader.
 */
spl_autoload_register( 'acf_component_manager_autoloader' );

function acf_component_manager_autoloader( $class ) {
	$namespace = 'AcfComponentManager';

	if ( strpos( $class, $namespace ) !== 0 ) {
		return;
	}

	$class = str_replace( $namespace, '', $class );
	//$class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
	$class_parts = explode( '\\', $class );
	//$class_parts = preg_split( '/^[A-Z]+$/', $class );
	$last_index = count( $class_parts ) -1;
	//$caps = '/^[A-Z]+$/';
	$caps = '/[A-Z]/';
	$caps = '/~[A-Z]~/';
	$caps = '/(?=[A-Z])/';
	$class_file_parts = preg_split( $caps, lcfirst( $class_parts[$last_index] ) );
	$class_file_name  = 'class-' . implode( '-', $class_file_parts ) . '.php';
	$class_parts[$last_index] = strtolower( $class_file_name );
	$class = implode( DIRECTORY_SEPARATOR, $class_parts );

	$path = ACF_COMPONENT_MANAGER_PATH . 'src' . $class;

	if ( file_exists( $path ) ) {
		require_once $path;
	}
	else {
		die( $path );
	}
}
