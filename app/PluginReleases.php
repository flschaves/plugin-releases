<?php
namespace PluginReleases;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginReleases {
	/**
	 * The single instance of the class.
	 *
	 * @var PluginReleases
	 * @since 0.1.0
	 */
	protected static $_instance = null;

	/**
	 * Admin instance.
	 *
	 * @var Admin
	 * @since 0.1.0
	 */
	public $admin = null;

	/**
	 * Admin instance.
	 *
	 * @var Api
	 * @since 0.1.0
	 */
	public $api = null;

	/**
	 * Admin instance.
	 *
	 * @var Features
	 * @since 0.1.0
	 */
	public $features = null;

	/**
	 * Class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->admin    = new Admin\Admin();
		$this->api      = new Api\Api();
		$this->features = new Features\Features();
	}

	/**
	 * Main PluginReleases instance.
	 *
	 * Ensures only one instance of PluginReleases is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 * @see pluginReleases()
	 * @return PluginReleases - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}
