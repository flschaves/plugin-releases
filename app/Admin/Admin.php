<?php 
namespace PluginReleases\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {
	/**
	 * Admin Area slug.
	 *
	 * @since 0.1.0
	 * 
	 * @var string
	 */
	private $slug = 'plugin-releases';

	/**
	 * List of admin area pages.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	private $pages;

	/**
	 * Dashboard instance.
	 *
	 * @since 0.1.0
	 *
	 * @var Dashboard
	 */
	public $dashboard = null;

	/**
	 * Class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->hooks();

		$this->dashboard = new Dashboard();
	}

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 0.1.0
	 */
	protected function hooks() {
		add_action( 'admin_menu', [ $this, 'addMenus' ] );
		add_action( 'admin_init', [ $this, 'processActions' ] );
		add_action( 'admin_bar_menu', [ $this, 'adminBarMenu' ], 1000 );
		add_action( 'admin_enqueue_scripts', [ $this, 'loadAdminStyles' ] );
	}

	/**
	 * Load admin styles.
	 *
	 * @since 0.1.1
	 *
	 * @return void
	 */
	public function loadAdminStyles() {
		wp_enqueue_style( 'plugin-releases', PLUGIN_RELEASES_URL . 'assets/css/admin.css', [], PLUGIN_RELEASES_VER );
	}

	/**
	 * Add admin area menu item.
	 *
	 * @since 0.1.0
	 */
	public function addMenus() {
		add_menu_page(
			esc_html__( 'Plugin Releases', 'plugin-releases' ),
			esc_html__( 'Plugin Releases', 'plugin-releases' ),
			$this->getMenuItemCapability(),
			$this->slug,
			[ $this, 'render' ],
			'dashicons-image-filter',
			$this->getMenuItemPosition()
		);
	}

	/**
	 * Add admin bar menu item.
	 *
	 * @since 0.1.0
	 */
	public function adminBarMenu() {
		global $wp_admin_bar;

		$count     = 0;
		$countHtml = '';
		$flashing  = false;

		$awaitingReview = pluginReleases()->features->getPRsAwaitingReview();
		if ( ! empty( $awaitingReview ) ) {
			$count+= count( $awaitingReview );
			$flashing = true;

			$wp_admin_bar->add_menu( [
				'id'    => 'plugin-releases-awaiting-review',
				'parent' => 'plugin-releases',
				'title' => '<span class="text">' . esc_html__( 'Awaiting your review:', 'plugin-releases' ) . '</span> ' . count( $awaitingReview ),
				'href'  => $this->getAdminUrl(), // TODO: Add link to the github filtered page.
			] );
		}

		if ( 0 < $count ) {
			$countHtml = '<div class="wp-core-ui wp-ui-notification ' . ( true === $flashing ? 'flashing' : '' ) . '">' . $count . '</div>';
		}

		$wp_admin_bar->add_menu( [
			'id'    => 'plugin-releases',
			'title' => '<span class="text">' . esc_html__( 'Plugin Releases', 'plugin-releases' ) . '</span>' . $countHtml,
			'href'  => $this->getAdminUrl(),
		] );
	}

	/**
	 * Get menu item position.
	 *
	 * @since 0.1.0
	 *
	 * @return int
	 */
	public function getMenuItemPosition() {
		return 95;
	}

	/**
	 * Get menu item capability.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function getMenuItemCapability() {
		return 'manage_options';
	}

	/**
	 * Get all admin area pages.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function getPages() {
		if ( empty( $this->pages ) ) {
			$this->pages = [
				$this->slug => new Pages\Releases(),
			];
		}

		return $this->pages;
	}

	/**
	 * Get admin area page instance.
	 *
	 * @since 0.1.0
	 *
	 * @param  string      $slug Page slug.
	 * @return object|bool
	 */
	public function getPage( $slug = '' ) {
		if ( empty( $slug ) ) {
			$slug = ! empty( $_GET['page'] ) ? \sanitize_key( $_GET['page'] ) : '';
		}

		$pages = $this->getPages();

		if ( isset( $pages[ $slug ] ) ) {
			return $pages[ $slug ];
		}

		return false;
	}

	/**
	 * Get admin area page URL.
	 *
	 * @since 0.1.0
	 *
	 * @return string URL.
	 */
	public function getAdminUrl() {
		return menu_page_url( $this->slug, false );
	}

	/**
	 * Handle POST submits.
	 *
	 * @since 0.1.0
	 */
	public function processActions() {
		$page = $this->getPage();

		// Prevent if is not or own page
		if ( ! $page ) {
			return;
		}

		// Process POST only if it exists.
		if ( is_callable( [ $page, 'processPost' ] ) && ! empty( $_POST['plugin-releases'] ) ) {
			$page->processPost( $_POST['plugin-releases'] );
		}
	}

	/**
	 * Render the admin area page.
	 *
	 * @since 0.1.0
	 */
	public function render() {
		$page = $this->getPage();

		if ( ! $page ) {
			return;
		}
		?>
			<div class="wrap" id="table-area">
				<h1>
					<?php
					esc_html_e( 'Plugin Releases', 'plugin-releases' );
					if ( method_exists( $page, 'getTitle' ) && ! empty( $page->getTitle() ) ) {
						echo " - {$page->getTitle()}";
					}
					?>
				</h1>

				<?php $page->render(); ?>
			</div>
		<?php
	}
}