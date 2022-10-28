<?php
/**
 * Main config file for loading WPP.
 *
 * DO NOT EDIT THIS FILE.
 *
 * All configuration should be done either in your project's `composer.json` file
 * or `.config/` directory.
 *
 * phpcs:disable PSR1.Files.SideEffects
 *
 */

// Load an escape hatch early load file, if it exists.
if ( is_readable( __DIR__ . '/.config/load-early.php' ) ) {
	include_once __DIR__ . '/.config/load-early.php';
}

// Load the plugin API (like add_action etc) early, so everything loaded
// via the Composer autoloaders can using actions.
require_once __DIR__ . '/wordpress/wp-includes/plugin.php';

// Load the whole autoloader very early, this will also include
// all `autoload.files` from all modules.
require_once __DIR__ . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/wordpress/' );
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', __DIR__ . '/content' );
}

// Pass "https" protocol from reverse proxies
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
  $_SERVER['HTTPS'] = 'on';
}

$protocol = ! empty( $_SERVER['HTTPS'] ) ? 'https' : 'http';
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	define( 'WP_CONTENT_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/content' );
}

if ( ! defined( 'WP_INITIAL_INSTALL' ) || ! WP_INITIAL_INSTALL ) {
	// Multisite is always enabled, unless some spooky
	// early loading code tried to change that of course.
	if ( ! defined( 'MULTISITE' ) ) {
		define( 'MULTISITE', true );
	}
}

if ( ! isset( $table_prefix ) ) {
	$table_prefix = getenv( 'TABLE_PREFIX' ) ?: 'wp_';
}

define( 'WP_HOME', $protocol . '://' . $_SERVER['HTTP_HOST']  );
define( 'WP_SITEURL', $protocol . '://' . $_SERVER['HTTP_HOST'] . "/wordpress" );

/*
 * DB constants are expected to be provided by other modules, as they are
 * environment specific.
 */
$required_constants = [
	'DB_HOST' => getenv('MYSQL_HOST'),
	'DB_NAME' => getenv('MYSQL_DATABASE'),
	'DB_USER' => getenv('MYSQL_USER'),
	'DB_PASSWORD' => getenv('MYSQL_PASSWORD'),
];

foreach ( $required_constants as $constant => $value ) {
	if ( ! defined( $constant ) ) {
		define($constant, $value);
	}
}

if ( ! getenv( 'WP_PHPUNIT__TESTS_CONFIG' ) ) {
	require_once ABSPATH . 'wp-settings.php';
}
