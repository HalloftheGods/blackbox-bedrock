<?php
namespace BlackBOX\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Bar_Toggle {

	public function __construct() {
		// Do not load if master constant kill-switch is active
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
	 * Register the toggle node in the WordPress Admin Bar.
	 *
	 * Placed inside top-secondary, adjacent to the user profile area (#wp-admin-bar-my-account).
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar
	 */
	public function register_admin_bar_toggle( $wp_admin_bar ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$is_active = $this->is_theme_active();
		$state_class = $is_active ? 'bb-theme-active' : 'bb-theme-inactive';
		$tooltip = $is_active
			? __( 'BlackBOX Bedrock: Active (Click to switch to Classic WordPress Theme)', 'blackbox-bedrock' )
			: __( 'BlackBOX Bedrock: Disabled (Click to activate BlackBOX Bedrock Theme)', 'blackbox-bedrock' );

		$title = sprintf(
			'<span class="bb-ab-toggle %s" data-active="%s" title="%s">' .
				'<span class="bb-ab-toggle-track">' .
					'<span class="bb-ab-toggle-thumb">' .
						'<span class="bb-ab-toggle-glow"></span>' .
					'</span>' .
				'</span>' .
				'<span class="bb-ab-toggle-label">%s</span>' .
			'</span>',
			esc_attr( $state_class ),
			$is_active ? '1' : '0',
			esc_attr( $tooltip ),
			$is_active ? 'BlackBOX' : 'Classic'
		);

		$wp_admin_bar->add_node( [
			'id'     => 'blackbox-bedrock-toggle',
			'parent' => 'top-secondary',
			'title'  => $title,
			'href'   => '#',
			'meta'   => [
				'class'    => 'blackbox-bedrock-toggle-node ' . $state_class,
				'title'    => $tooltip,
				'tabindex' => 0,
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
			/* Admin Bar Node Layout */
			#wpadminbar #wp-admin-bar-blackbox-bedrock-toggle {
				display: inline-flex !important;
				align-items: center !important;
				order: -1 !important;
				margin: 0 4px !important;
			}

			#wpadminbar #wp-admin-bar-blackbox-bedrock-toggle .ab-item {
				display: inline-flex !important;
				align-items: center !important;
				justify-content: center !important;
				height: 100% !important;
				padding: 0 6px !important;
				max-width: none !important;
				font-size: 11px !important;
				line-height: normal !important;
				text-indent: 0 !important;
				background: transparent !important;
				cursor: pointer !important;
			}

			#wpadminbar #wp-admin-bar-blackbox-bedrock-toggle .ab-item * {
				display: inline-flex !important;
				visibility: visible !important;
				opacity: 1 !important;
				font-size: inherit !important;
				line-height: inherit !important;
			}

			/* Toggle Component */
			.bb-ab-toggle {
				display: inline-flex;
				align-items: center;
				gap: 7px;
				cursor: pointer;
				user-select: none;
			}

			.bb-ab-toggle-track {
				position: relative;
				display: inline-flex;
				align-items: center;
				width: 34px;
				height: 18px;
				border-radius: 10px;
				background: rgba(255, 255, 255, 0.15);
				border: 1px solid rgba(255, 255, 255, 0.25);
				transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
				box-sizing: border-box;
			}

			.bb-theme-active .bb-ab-toggle-track {
				background: rgba(14, 28, 48, 0.85);
				border-color: #62c9ff;
				box-shadow: 0 0 8px rgba(98, 201, 255, 0.35);
			}

			.bb-theme-inactive .bb-ab-toggle-track {
				background: rgba(0, 0, 0, 0.2);
				border-color: rgba(255, 255, 255, 0.3);
			}

			.bb-ab-toggle-thumb {
				position: absolute;
				top: 1px;
				left: 1px;
				width: 14px;
				height: 14px;
				border-radius: 50%;
				background: #999;
				transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), background 0.25s ease, box-shadow 0.25s ease;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.bb-theme-active .bb-ab-toggle-thumb {
				transform: translateX(16px);
				background: #62c9ff;
				box-shadow: 0 0 6px #62c9ff, 0 0 12px rgba(98, 201, 255, 0.8);
			}

			.bb-theme-inactive .bb-ab-toggle-thumb {
				transform: translateX(1px);
				background: #bbb;
				box-shadow: none;
			}

			.bb-ab-toggle-label {
				font-size: 11px !important;
				font-weight: 600 !important;
				letter-spacing: 0.3px !important;
				color: rgba(255, 255, 255, 0.85) !important;
				text-transform: uppercase !important;
			}

			.bb-theme-active .bb-ab-toggle-label {
				color: #62c9ff !important;
				text-shadow: 0 0 8px rgba(98, 201, 255, 0.4) !important;
			}

			.bb-ab-toggle.bb-is-loading {
				opacity: 0.6;
				pointer-events: none;
			}
		</style>
		<script id="blackbox-admin-bar-toggle-js">
			(function() {
				function initBlackBoxToggle() {
					var node = document.getElementById('wp-admin-bar-blackbox-bedrock-toggle');
					if (!node || node.dataset.bbBound) return;
					node.dataset.bbBound = '1';

					var link = node.querySelector('.ab-item');
					if (!link) return;

					link.addEventListener('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var toggle = node.querySelector('.bb-ab-toggle');
						if (!toggle || toggle.classList.contains('bb-is-loading')) return;

						toggle.classList.add('bb-is-loading');

						// Tactile optimistic visual feedback
						var isActive = toggle.getAttribute('data-active') === '1';
						if (isActive) {
							toggle.classList.remove('bb-theme-active');
							toggle.classList.add('bb-theme-inactive');
							node.classList.remove('bb-theme-active');
							node.classList.add('bb-theme-inactive');
							toggle.setAttribute('data-active', '0');
						} else {
							toggle.classList.remove('bb-theme-inactive');
							toggle.classList.add('bb-theme-active');
							node.classList.remove('bb-theme-inactive');
							node.classList.add('bb-theme-active');
							toggle.setAttribute('data-active', '1');
						}

						var data = new URLSearchParams();
						data.append('action', 'blackbox_toggle_theme');
						data.append('nonce', '<?php echo esc_js( $nonce ); ?>');

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
								toggle.classList.remove('bb-is-loading');
								alert('Could not toggle theme: ' + (result.data || 'Unknown error'));
							}
						})
						.catch(function(err) {
							toggle.classList.remove('bb-is-loading');
							console.error('BlackBOX Bedrock toggle error:', err);
						});
					});
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
	 * AJAX endpoint to flip the BlackBOX Bedrock theme enabled/disabled state.
	 */
	public function ajax_toggle_theme() {
		check_ajax_referer( 'blackbox_toggle_theme_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( __( 'Unauthorized', 'blackbox-bedrock' ), 403 );
		}

		$current = get_option( 'xophz_compass_disable_mu_styles', '0' );
		$is_disabled = ( ! empty( $current ) && $current !== '0' );
		$new_val = $is_disabled ? '0' : '1';

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
