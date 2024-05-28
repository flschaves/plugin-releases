<?php 
namespace PluginReleases\Features;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Features {
	/**
	 * Class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
	}

	/**
	 * Get pull requests.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function getPullRequests() {
		if ( false === ( $pullRequests = get_transient( 'plugin_releases_pull_requests' ) ) ) {
			$response = pluginReleases()->api->github->get('/repos/:owner/:repo/pulls', [
				'owner' => 'awesomemotive',
				'repo'  => 'aioseo'
			] );

			$pullRequests = json_decode( wp_remote_retrieve_body( $response ) );

			set_transient( 'plugin_releases_pull_requests', $pullRequests, 4 * HOUR_IN_SECONDS );
		}

		return $pullRequests;
	}

	/**
	 * Get Pull Requests awaiting your review.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function getPRsAwaitingReview() {
		$pullRequests = $this->getPullRequests();
		$awaitingYou  = [];

		if ( ! empty( $pullRequests ) ) {
			foreach ( $pullRequests as $pr ) {
				if ( empty( $pr->requested_reviewers ) ) {
					continue;
				}

				foreach ( $pr->requested_reviewers as $reviewer ) {
					if ( 'flschaves' === $reviewer->login ) {
						$awaitingYou[] = $pr;
					}
				}
			}
		}

		return $awaitingYou;
	}
}