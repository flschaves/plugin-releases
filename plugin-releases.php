<?php
/**
 * Plugin Name:     Plugin Releases
 * Plugin URI:      https://aioseo.com
 * Description:     
 * Author:          Filipe Chaves
 * Text Domain:     plugin-releases
 * Domain Path:     /languages
 * Version:         0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! defined( 'PLUGIN_RELEASES_VER' ) ) {
	define( 'PLUGIN_RELEASES_VER', '0.1.0' );
}
if ( ! defined( 'PLUGIN_RELEASES_DIR' ) ) {
	define( 'PLUGIN_RELEASES_DIR', __DIR__ );
}
if ( ! defined( 'PLUGIN_RELEASES_FILE' ) ) {
	define( 'PLUGIN_RELEASES_FILE', __FILE__ );
}

/**
 * Returns the main instance of PluginReleases.
 *
 * @since  0.1.0
 * @return PluginReleases
 */
function pluginReleases() {
	return \PluginReleases\PluginReleases::instance();
}

pluginReleases();