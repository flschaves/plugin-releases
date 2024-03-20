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
			'plugin-releases',
			__( 'Plugin Releases', 'plugin-releases' ),
			[ $this, 'render' ],
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
	public function render() {
		$awaiting = pluginReleases()->features->getPRsAwaitingReview();
		if ( ! empty( $awaiting ) ) {
			echo '<h3>Pull Requests Awaiting Your Review (' . count( $awaiting ) . '):</h3>';

			echo '<ul>';
			foreach ( $awaiting as $pr ) {
				echo '<li><a href="' . esc_url( $pr->html_url ) . '" target="_blank">' . esc_html( $pr->title ) . '</a></li>';
			}
			echo '</ul>';
		}
	}
}