const mix = require( 'laravel-mix' );

/**
 * Build preferences
 */
mix
	.setPublicPath( './build' )
	.options( { processCssUrls: false } );


/**
 * Javascripts
 */
mix
	.js( 'assets/js/input.js', 'js' )
	.js( 'assets/js/input-group.js', 'js' );

/**
 * Stylesheets
 */
mix
	.sass( 'assets/scss/input.scss', 'css' )
