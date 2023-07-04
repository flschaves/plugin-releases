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
	 * Class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 0.1.0
	 */
	protected function hooks() {
		add_action( 'admin_menu', [ $this, 'addMenus' ] );
		add_action( 'admin_init', [ $this, 'processActions' ] );
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