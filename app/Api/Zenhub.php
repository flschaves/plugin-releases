<?php 
namespace PluginReleases\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Zenhub extends BaseApi {
	/**
	 * Class constructor for Zenhub API.
	 *
	 * @since 0.1.0
	 * @see   https://github.com/ZenHubIO/API
	 */
	public function __construct() {
		if ( ! defined( 'PLUGIN_RELEASES_ZENHUB_TOKEN' ) || empty( PLUGIN_RELEASES_ZENHUB_TOKEN ) ) {
			return;
		}

		$this->apiUrl  = 'https://api.zenhub.com';
		$this->headers = [
			'X-Authentication-Token' => PLUGIN_RELEASES_ZENHUB_TOKEN,
		];
	}
}