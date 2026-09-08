<?php
namespace BlackBOX\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Bar_Toggle {

	public function __construct() {
		if ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) {
			return;
		}

		add_action( 'admin_bar_menu', [ $this, 'register_admin_bar_toggle' ], 1 );
		add_action( 'admin_head', [ $this, 'output_toggle_assets' ], 999 );
		add_action( 'wp_head', [ $this, 'output_toggle_assets' ], 999 );
		add_action( 'wp_ajax_blackbox_toggle_theme', [ $this, 'ajax_toggle_theme' ] );
	}

	/**
	 * Check whether BlackBOX Bedrock styling is currently active.
	 *
	 * @return bool True if theme styles are active, false if disabled.
	 */
	public function is_theme_active() {
		$disabled = get_option( 'xophz_compass_disable_mu_styles', '0' );
		return empty( $disabled );
	}

	/**
	 * Register the toggle node in the WordPress Admin Bar as a native menu item.
	 *
	 * Placed inside top-secondary, adjacent to the user profile area (#wp-admin-bar-my-account).
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar
	 */
	public function register_admin_bar_toggle( $wp_admin_bar ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$is_active    = $this->is_theme_active();
		$state_class  = $is_active ? 'bb-theme-active' : 'bb-theme-inactive';
		$status_label = $is_active ? 'ON' : 'OFF';
		$status_color = $is_active ? '#62c9ff' : '#dba617';
		$tooltip      = $is_active
			? __( 'BlackBOX Bedrock is Active. Click to switch to Classic WordPress theme.', 'blackbox-bedrock' )
			: __( 'BlackBOX Bedrock is Disabled. Click to activate BlackBOX Bedrock theme.', 'blackbox-bedrock' );

		// Top-level native admin bar node
		$wp_admin_bar->add_node( [
			'id'     => 'blackbox-theme-toggle',
			'parent' => 'top-secondary',
			'title'  => sprintf(
				'<span class="ab-icon dashicons dashicons-admin-appearance" aria-hidden="true"></span>' .
				'<span class="ab-label">%s: <strong class="bb-theme-status-text" style="color:%s;">%s</strong></span>',
				esc_html__( 'BlackBOX', 'blackbox-bedrock' ),
				esc_attr( $status_color ),
				esc_html( $status_label )
			),
			'href'   => '#',
			'meta'   => [
				'class'    => 'blackbox-theme-toggle-node ' . $state_class,
				'title'    => $tooltip,
				'tabindex' => 0,
			],
		] );

		// Submenu Option 1: Activate BlackBOX
		$wp_admin_bar->add_node( [
			'id'     => 'blackbox-theme-opt-dark',
			'parent' => 'blackbox-theme-toggle',
			'title'  => ( $is_active ? '&#10003; ' : '&nbsp;&nbsp;&nbsp;' ) . __( 'BlackBOX Bedrock (Dark Glass)', 'blackbox-bedrock' ),
			'href'   => '#',
			'meta'   => [
				'class' => 'bb-menu-opt bb-opt-dark' . ( $is_active ? ' is-active' : '' ),
			],
		] );

		// Submenu Option 2: Classic WordPress
		$wp_admin_bar->add_node( [
			'id'     => 'blackbox-theme-opt-classic',
			'parent' => 'blackbox-theme-toggle',
			'title'  => ( ! $is_active ? '&#10003; ' : '&nbsp;&nbsp;&nbsp;' ) . __( 'Classic WordPress Theme', 'blackbox-bedrock' ),
			'href'   => '#',
			'meta'   => [
				'class' => 'bb-menu-opt bb-opt-classic' . ( ! $is_active ? ' is-active' : '' ),
			],
		] );

		// Submenu Option 3: Bedrock Dashboard link
		$dashboard_url = is_multisite() && is_network_admin()
			? network_admin_url( 'admin.php?page=blackbox' )
			: admin_url( 'admin.php?page=blackbox' );

		$wp_admin_bar->add_node( [
			'id'     => 'blackbox-theme-opt-dashboard',
			'parent' => 'blackbox-theme-toggle',
			'title'  => __( 'Bedrock Dashboard', 'blackbox-bedrock' ),
			'href'   => $dashboard_url,
			'meta'   => [
				'class' => 'bb-menu-opt bb-opt-dashboard',
			],
		] );
	}

	/**
	 * Injects minimal CSS and JS for the Admin Bar toggle.
	 */
	public function output_toggle_assets() {
		if ( ! is_admin_bar_showing() || ! is_user_logged_in() ) {
			return;
		}

		$nonce   = wp_create_nonce( 'blackbox_toggle_theme_nonce' );
		$ajaxurl = admin_url( 'admin-ajax.php' );
		?>
		<style id="blackbox-admin-bar-toggle-css">
			/* Ensure proper order in top-secondary */
			#wpadminbar #wp-admin-bar-blackbox-theme-toggle {
				order: -1 !important;
			}

			#wpadminbar #wp-admin-bar-blackbox-theme-toggle.bb-theme-active > .ab-item .ab-icon {
				color: #62c9ff !important;
			}

			#wpadminbar #wp-admin-bar-blackbox-theme-toggle.bb-is-loading {
				opacity: 0.6 !important;
				pointer-events: none !important;
			}

			#wpadminbar #wp-admin-bar-blackbox-theme-toggle .bb-menu-opt.is-active > .ab-item {
				font-weight: 600 !important;
				color: #62c9ff !important;
			}
		</style>
		<script id="blackbox-admin-bar-toggle-js">
			(function() {
				function triggerThemeToggle(targetState) {
					var node = document.getElementById('wp-admin-bar-blackbox-theme-toggle');
					if (node) {
						node.classList.add('bb-is-loading');
					}

					var data = new URLSearchParams();
					data.append('action', 'blackbox_toggle_theme');
					data.append('nonce', '<?php echo esc_js( $nonce ); ?>');
					if (typeof targetState !== 'undefined' && targetState !== null) {
						data.append('target_state', targetState);
					}

					fetch('<?php echo esc_url( $ajaxurl ); ?>', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded; credentials=same-origin'
						},
						body: data.toString()
					})
					.then(function(res) { return res.json(); })
					.then(function(result) {
						if (result && result.success) {
							window.location.reload();
						} else {
							if (node) node.classList.remove('bb-is-loading');
							alert('Could not switch theme: ' + (result.data || 'Unknown error'));
						}
					})
					.catch(function(err) {
						if (node) node.classList.remove('bb-is-loading');
						console.error('BlackBOX Bedrock toggle error:', err);
					});
				}

				function initBlackBoxToggle() {
					var node = document.getElementById('wp-admin-bar-blackbox-theme-toggle');
					if (!node || node.dataset.bbBound) return;
					node.dataset.bbBound = '1';

					// Top-level item direct click: toggle
					var topLink = node.querySelector(':scope > .ab-item');
					if (topLink) {
						topLink.addEventListener('click', function(e) {
							e.preventDefault();
							e.stopPropagation();
							triggerThemeToggle(null);
						});
					}

					// Submenu option: Dark mode
					var optDark = document.getElementById('wp-admin-bar-blackbox-theme-opt-dark');
					if (optDark) {
						var darkLink = optDark.querySelector('.ab-item');
						if (darkLink) {
							darkLink.addEventListener('click', function(e) {
								e.preventDefault();
								e.stopPropagation();
								triggerThemeToggle('0');
							});
						}
					}

					// Submenu option: Classic mode
					var optClassic = document.getElementById('wp-admin-bar-blackbox-theme-opt-classic');
					if (optClassic) {
						var classicLink = optClassic.querySelector('.ab-item');
						if (classicLink) {
							classicLink.addEventListener('click', function(e) {
								e.preventDefault();
								e.stopPropagation();
								triggerThemeToggle('1');
							});
						}
					}
				}

				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', initBlackBoxToggle);
				} else {
					initBlackBoxToggle();
				}
			})();
		</script>
		<?php
	}

	/**
	 * AJAX endpoint to flip or set the BlackBOX Bedrock theme state.
	 */
	public function ajax_toggle_theme() {
		check_ajax_referer( 'blackbox_toggle_theme_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( __( 'Unauthorized', 'blackbox-bedrock' ), 403 );
		}

		if ( isset( $_POST['target_state'] ) ) {
			$target = sanitize_text_field( wp_unslash( $_POST['target_state'] ) );
			$new_val = ( $target === '0' || $target === 'active' ) ? '0' : '1';
		} else {
			$current = get_option( 'xophz_compass_disable_mu_styles', '0' );
			$is_disabled = ( ! empty( $current ) && $current !== '0' );
			$new_val = $is_disabled ? '0' : '1';
		}

		update_option( 'xophz_compass_disable_mu_styles', $new_val );

		if ( is_multisite() ) {
			update_site_option( 'xophz_compass_disable_mu_styles', $new_val );
		}

		update_user_meta( get_current_user_id(), 'xophz_compass_disable_mu_styles', $new_val );

		wp_send_json_success( [
			'disabled'  => ( $new_val === '1' ),
			'is_active' => ( $new_val === '0' ),
		] );
	}
}
