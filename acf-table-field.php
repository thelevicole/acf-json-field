<?php

/**
 * Plugin Name: ACF JSON Field
 * Description: A simple JSON editor field for ACF.
 * Version: 1.0.0
 * Plugin URI: https://skape.co/
 * Author: Skape Collective
 * Author URI: https://skape.co/
 * Text Domain: skape
 * Network: false
 * Requires at least: 5.0.0
 * Requires PHP: 7.2
 */

require_once plugin_dir_path( __FILE__ ) . 'core/Autoload.php';
$autoload = new AcfJsonField\Autoload( plugin_dir_path( __FILE__ ) );

$autoload->loadArray( [
	'AcfJsonField\\' => 'core'
], 'psr-4' );

// Register global constants
AcfJsonField\Utilities\Constants::set( 'DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
AcfJsonField\Utilities\Constants::set( 'VERSION', '1.0.0' );
AcfJsonField\Utilities\Constants::set( 'PATH', plugin_dir_path( __FILE__ ) );
AcfJsonField\Utilities\Constants::set( 'URL', plugin_dir_url( __FILE__ ) );
AcfJsonField\Utilities\Constants::set( 'BASENAME', plugin_basename( __FILE__ ) );

// Register field
add_action( 'acf/include_field_types', function() {
	new AcfJsonField\FieldV5;
} );
