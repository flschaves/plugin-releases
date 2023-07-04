<?php
namespace PluginReleases\Admin\Pages;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Releases {
	/**
	 * Get the page title.
	 *
	 * @since 0.1.0
	 * 
	 * @return string The page title.
	 */
	public function getTitle() {
		if ( ! empty( $_GET['release_title'] ) ) {
			return esc_html( $_GET['release_title'] );
		}

		return '';
	}

	/**
	 * Render the page HTML.
	 *
	 * @since 0.1.0
	 */
	public function render() {
		if ( ! empty( $_GET['release_id'] ) ) {
			$this->renderIssues( $_GET['release_id'] );
		} else {
			$this->renderReleases();
		}
	}

	/**
	 * Render releases.
	 *
	 * @since 0.1.0
	 *
	 * @return null
	 */
	private function renderReleases() {
		$response = pluginReleases()->api->zenhub->get('/p1/repositories/:repo_id/reports/releases', [
			'repo_id' => PLUGIN_RELEASES_GITHUB_REPO_ID,
		] );

		$releases = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $response ) ) {
			echo 'No releases found.';
			return;
		}

		// Sort by Release Title.
		usort( $releases, function( $a, $b ) { return strcmp( $b->title, $a->title ); } );

		?>
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th>Release</th>
					<th>Start date</th>
					<th>Desired end date</th>
					<th>State</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				foreach ( $releases as $release ) {
					?>
					<tr>
						<td><a href="<?php echo esc_url( $this->getReleaseUrl( $release ) ) ?>"><?php echo esc_html( $release->title ); ?></a></td>
						<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $release->start_date ) ); ?></td>
						<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $release->desired_end_date ) ); ?></td>
						<td><?php echo esc_html( ucfirst( $release->state ) ); ?></td>
						<td>
							<a href="<?php echo esc_url( $this->getReleaseUrl( $release ) ) ?>">List issues</a>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php 
	}

	/**
	 * Render the issues for a release.
	 *
	 * @since 0.1.0
	 *
	 * @param  int  $release_id Release ID.
	 * @return null
	 */
	private function renderIssues( $releaseId ) {
		$issues = $this->getReleaseIssues( $releaseId );

		if ( empty( $issues ) ) {
			echo 'No issues found.';
			return;
		}

		?>
		<table class="widefat striped release-issues">
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Created by</th>
					<th>Assignee</th>
					<th>Created at</th>
					<th>Labels</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				foreach ( $issues as $issue ) {
					?>
					<tr>
						<td>
							<a href="<?php echo esc_url( $issue->githubIssue->html_url ); ?>" target="_blank">
								#<?php echo esc_html( $issue->githubIssue->number ); ?>
							</a>
							(<?php echo esc_html( $issue->githubIssue->state ); ?>)
						</td>
						<td><?php echo esc_html( $issue->githubIssue->title ); ?></td>
						<td>
							<a href="<?php echo esc_url( $issue->githubIssue->user->html_url ); ?>" target="_blank">
								<img src="<?php echo esc_url( $issue->githubIssue->user->avatar_url ); ?>" alt="<?php echo esc_attr( $issue->githubIssue->user->login ); ?>" title="<?php echo esc_attr( $issue->githubIssue->user->login ); ?>">
							</a>
						</td>
						<td>
							<?php foreach ( $issue->githubIssue->assignees as $assignee ) { ?>
								<a href="<?php echo esc_url( $assignee->html_url ); ?>" target="_blank">
									<img src="<?php echo esc_url( $assignee->avatar_url ); ?>" alt="<?php echo esc_attr( $assignee->login ); ?>" title="<?php echo esc_attr( $assignee->login ); ?>">
								</a>
							<?php } ?>
						</td>
						<td>
							<?php echo date_i18n( get_option( 'date_format' ), strtotime( $issue->githubIssue->created_at ) ); ?>
						</td>
						<td>
							<?php foreach ( $issue->githubIssue->labels as $label ) { ?>
								<span class="label" style="background-color: #<?php echo esc_attr( $label->color ); ?>;" title="<?php echo esc_attr( $label->description ); ?>">
									<?php echo esc_html( $label->name ); ?>
								</span>
							<?php } ?>
						</td>
						<td>
							<a href="<?php echo esc_url( $issue->githubIssue->html_url ); ?>" target="_blank">View on GitHub</a>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>

		<style>
			.release-issues img {
				width: 30px;
				height: 30px;
				border-radius: 50%;
			}

			.release-issues .label {
				display: inline-block;
				border-radius: 3px;
				margin-right: 3px;
				padding: 2px 4px;
				color: #fff;
				font-size: 10px;
				line-height: 1;
			}
		</style>
		<?php 
	}

	/**
	 * Get the URL for a release.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $release The release object.
	 * @return string           Release URL.
	 */
	private function getReleaseUrl( $release ) {
		return add_query_arg( [
			'release_id' => $release->release_id,
			'release_title' => $release->title
		], pluginReleases()->admin->getAdminUrl() );
	}

	/**
	 * Get the issues for a release.
	 *
	 * @since 0.1.0
	 *
	 * @param  int    $release_id Release ID.
	 * @return array              Issues.
	 */
	private function getReleaseIssues( $releaseId ) {
		if ( false === ( $issues = get_transient( 'plugin_releases_' . $releaseId ) ) ) {
			$response = pluginReleases()->api->zenhub->get('/p1/reports/release/:release_id/issues', [
				'release_id' => $releaseId,
			] );

			$issues = json_decode( wp_remote_retrieve_body( $response ) );

			foreach ( $issues as $issue ) {
				$response = pluginReleases()->api->github->get('repositories/:repo_id/issues/:issue_id', [
					'repo_id'  => $issue->repo_id,
					'issue_id' => $issue->issue_number
				] );

				$githubIssue = json_decode( wp_remote_retrieve_body( $response ) );

				$issue->githubIssue = $githubIssue;
			}

			set_transient( 'plugin_releases_' . $releaseId, $issues, 12 * HOUR_IN_SECONDS );
		}

		return $issues;
	}

	/**
	 * Process the POST request.
	 *
	 * @since 0.1.0
	 *
	 * @param array $post Posted data.
	 */
	public function processPost( $post ) {}
}