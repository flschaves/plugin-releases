<?php 
namespace PluginReleases\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard {

	/**
	 * Class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', [ $this, 'addWidgets' ] );
	}

	/**
	 * Adds the widgets.
	 *
	 * @since 0.1.0
	 */
	public function addWidgets() {
		wp_add_dashboard_widget(
			'plugin-releases-awaiting-review',
			__( 'Awaiting Review', 'plugin-releases' ),
			[ $this, 'renderAwaitingReview' ],
			null,
			null,
			'normal',
			'high'
		);
	}

	/**
	 * Render the dashboard Awaiting Review widget.
	 *
	 * @since 0.1.0
	 */
	public function renderAwaitingReview() {
		$awaiting = pluginReleases()->features->getPRsAwaitingReview();
		echo count($awaiting);
	}
}