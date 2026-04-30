<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin {
	private static $grouped_wpmudev_icons = [];
	private static $grouped_wpmudev_names = [];

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_w4_protocol_menu' ] );
		add_action( 'admin_menu', [ $this, 'register_blackbox_menu' ] );
		add_action( 'admin_menu', [ $this, 'group_wpmudev_plugins' ], 9999 );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_filter( 'wp_theme_json_data_theme', [ $this, 'override_editor_theme_json' ] );
		add_filter( 'block_editor_settings_all', [ $this, 'force_editor_css_settings' ], 9999, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_print_styles', [ $this, 'enqueue_styles' ], 9999 );
		add_filter( 'style_loader_tag', [ $this, 'inject_into_install_tag' ], 9999, 2 );
		add_filter( 'script_loader_tag', [ $this, 'inject_into_install_scripts' ], 9999, 2 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'inject_iframe_class' ], 1 );
		add_filter( 'mce_css', [ $this, 'add_classic_editor_dark_css' ] );
		add_action( 'admin_footer', [ Core::class, 'inject_canvas_script' ], 9999 );
		add_action( 'wp_ajax_blackbox_toggle_plugin', [ $this, 'ajax_toggle_plugin' ] );

		if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
			// Force inject on install.php if footer hook doesn't fire early enough
			add_action( 'admin_head', [ Core::class, 'inject_canvas_script' ], 9999 );
			add_action( 'admin_head', function() {
				echo '<script>document.addEventListener("DOMContentLoaded", function() { document.body.classList.add("body-glass"); });</script>';
			}, 9999 );
		}

		add_action( 'admin_head', [ $this, 'output_theme_colors' ], 15 );
	}

	public function register_w4_protocol_menu() {
		$icon_url = plugins_url( 'assets/images/webwork.png', dirname( __DIR__ ) . '/BlackBOX.php' );
		
		add_menu_page(
			'w⁴ Protocol',
			'w⁴ Protocol',
			'manage_options',
			'w4-protocol',
			[ $this, 'settings_page_display' ],
			$icon_url,
			1 // Position 1 places it directly below My Compass (0) and above Dashboard (2)
		);

		add_submenu_page(
			'w4-protocol',
			'BlackBOX Bedrock',
			'BlackBOX Bedrock',
			'manage_options',
			'w4-protocol',
			[ $this, 'settings_page_display' ]
		);


		add_submenu_page(
			'w4-protocol',
			'YouMeOS',
			'YouMeOS',
			'manage_options',
			'w4-youmeos',
			[ $this, 'render_placeholder_page' ]
		);
	}

	public function render_placeholder_page() {
		echo '<div class="wrap"><h1>' . esc_html( get_admin_page_title() ) . '</h1><p>Placeholder configuration page.</p></div>';
	}

	public function register_blackbox_menu() {
		$icon_url = plugins_url( 'assets/images/obsidian.png', dirname( __DIR__ ) . '/BlackBOX.php' );
		
		add_menu_page(
			'BlackBOX',
			'BlackBOX',
			'manage_options',
			'blackbox-plugins',
			[ $this, 'render_blackbox_page' ],
			$icon_url,
			-1 // Position -1 places it at the absolute top
		);

		// We must explicitly register a submenu with the identical slug.
		// Otherwise, WordPress will auto-hijack the parent menu link to point to the first WPMUDEV plugin.
		add_submenu_page(
			'blackbox-plugins',
			'Operations Matrix',
			'Operations Matrix',
			'manage_options',
			'blackbox-plugins',
			[ $this, 'render_blackbox_page' ]
		);
	}

	public function render_blackbox_page() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$all_plugins = get_plugins();
		$active_plugins = (array) get_option( 'active_plugins', [] );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', [] ) ) );
		}

		$wpmudev_slugs = [
			'beehive-analytics',
			'broken-link-checker',
			'forminator',
			'hustle',
			'shipper',
			'snapshot-backups',
			'ultimate-branding',
			'wp-defender',
			'wp-hummingbird',
			'wp-smush-pro',
			'wpmu-dev-seo',
			'wpmudev-updates',
			'wpmudev-videos'
		];

		$display_plugins = [];

		$folder_to_slug = [
			'beehive-analytics' => 'beehive',
			'broken-link-checker' => 'blc_dash',
			'forminator' => 'forminator',
			'hustle' => 'hustle',
			'shipper' => 'shipper',
			'snapshot-backups' => 'snapshot',
			'ultimate-branding' => 'branding',
			'wp-defender' => 'wp-defender',
			'wp-hummingbird' => 'wphb',
			'wp-smush-pro' => 'smush',
			'wpmu-dev-seo' => 'wds_wizard',
			'wpmudev-updates' => 'wpmudev',
			'wpmudev-videos' => 'wpmudev-videos'
		];

		foreach ( $all_plugins as $path => $data ) {
			$folder = dirname( $path );
			$is_xophz = strpos( $folder, 'xophz-compass' ) !== false;
			$is_wpmudev = in_array( $folder, $wpmudev_slugs, true );

			if ( $is_xophz || $is_wpmudev ) {
				$is_active = in_array( $path, $active_plugins, true ) || is_plugin_active_for_network( $path );
				
				$icon = '';
				if ( $is_xophz ) {
					// Check for specific custom images first
					if ( $folder === 'xophz-compass-magic-formula' ) {
						$icon = plugins_url( 'xophz-compass/assets/magic-formula.svg' );
					} else {
						$icon_path = WP_PLUGIN_DIR . '/' . $folder . '/icon.svg';
						if ( file_exists( $icon_path ) ) {
							$icon = plugins_url( $folder . '/icon.svg' );
						}
					}
				}
				
				if ( empty($icon) && $is_wpmudev ) {
					$menu_slug = $folder_to_slug[ $folder ] ?? $folder;
					if ( isset( self::$grouped_wpmudev_icons[ $menu_slug ] ) ) {
						$icon = self::$grouped_wpmudev_icons[ $menu_slug ];
					}
				}
				
				if ( empty($icon) ) {
					// Fallback to obsidian if no custom menu icon was found
					$icon = plugins_url( 'assets/images/obsidian.png', dirname( __DIR__ ) . '/BlackBOX.php' );
				}

				// Resolve themed names
				$name = $data['Name'];
				if ( $is_xophz && class_exists( '\Xophz_Compass_Branding' ) ) {
					$slug = str_replace( 'xophz-compass-', '', $folder );
					if ( $slug === 'xophz-compass' ) {
						$slug = 'compass';
					}
					$default_name = trim( str_replace( 'Xophz', '', $data['Name'] ) );
					$name = \Xophz_Compass_Branding::get_plugin_name( $slug, $default_name );
				} else if ( $is_wpmudev ) {
					$menu_slug = $folder_to_slug[ $folder ] ?? $folder;
					if ( isset( self::$grouped_wpmudev_names[ $menu_slug ] ) ) {
						$name = self::$grouped_wpmudev_names[ $menu_slug ];
					} else {
						$name = str_replace( 'WPMU DEV', '', $data['Name'] );
						$name = trim( $name, ' -' );
					}
				}

				// Determine the Go URL
				$go_url = '';
				if ( $is_xophz ) {
					$route_map = [
						'quests' => 'questbook',
						'alphabet-soup' => 'newsroom'
					];
					$route_slug = $route_map[$slug] ?? $slug;
					
					if ( $slug === 'compass' ) {
						$go_url = admin_url( 'admin.php?page=xophz-compass' );
					} else {
						$go_url = admin_url( 'admin.php?page=xophz-compass#/' . $route_slug );
					}
				} else if ( $is_wpmudev ) {
					$go_url = admin_url( 'admin.php?page=' . $menu_slug );
				}

				$display_plugins[] = [
					'name' => $name,
					'desc' => $data['Description'],
					'version' => $data['Version'],
					'author' => $data['Author'],
					'active' => $is_active,
					'icon' => $icon,
					'type' => $is_xophz ? 'Compass Engine' : 'Infrastructure',
					'path' => $path,
					'go_url' => $go_url
				];
			}
		}

		// Sort purely by name alphabetically as requested
		usort( $display_plugins, function($a, $b) {
			return strcasecmp( $a['name'], $b['name'] );
		});

		?>
		<style>
			.blackbox-dashboard {
				margin: 2rem 2rem 2rem 0;
			}
			.blackbox-header {
				display: flex;
				align-items: center;
				gap: 20px;
				margin-bottom: 2rem;
				border-bottom: 1px solid rgba(255,255,255,0.1);
				padding-bottom: 20px;
			}
			.blackbox-header img {
				width: 64px;
				height: 64px;
			}
			.blackbox-header h1 {
				margin: 0;
				font-size: 2.5rem;
				border: none !important;
				padding: 0 !important;
			}
			.blackbox-grid {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
				gap: 24px;
			}
			.blackbox-card {
				background: var(--rough-glass-bg, rgba(13, 17, 23, 0.6));
				backdrop-filter: var(--rough-glass-filter, blur(20px));
				-webkit-backdrop-filter: var(--rough-glass-filter, blur(20px));
				border: 1px solid var(--rough-glass-border, rgba(255,255,255,0.05));
				border-radius: 12px;
				padding: 24px;
				display: flex;
				flex-direction: column;
				box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
				transition: all 0.3s ease;
				position: relative;
				overflow: hidden;
			}
			.blackbox-card:hover {
				transform: translateY(-5px);
				box-shadow: 0 12px 40px rgba(98, 201, 255, 0.15);
				border-color: rgba(98, 201, 255, 0.3);
			}
			.blackbox-card::before {
				content: '';
				position: absolute;
				top: 0; left: 0; right: 0; height: 4px;
				background: rgba(255,255,255,0.1);
			}
			.blackbox-card.is-active::before {
				background: var(--accent, #62c9ff);
			}
			.blackbox-card.is-infrastructure::before {
				background: var(--gold, #d9be6f);
			}
			.blackbox-card-header {
				display: flex;
				align-items: center;
				gap: 16px;
				margin-bottom: 16px;
			}
			.blackbox-card-icon {
				width: 48px;
				height: 48px;
				border-radius: 10px;
				object-fit: contain;
				background: rgba(0, 0, 0, 0.3);
				padding: 8px;
				border: 1px solid rgba(255,255,255,0.05);
			}
			.blackbox-card-title-area h2 {
				margin: 0 0 4px 0 !important;
				font-size: 1.25rem !important;
				border: none !important;
				padding: 0 !important;
				line-height: 1.2;
				color: var(--text-main);
			}
			.blackbox-card-meta {
				display: flex;
				gap: 8px;
				font-size: 0.75rem;
				color: rgba(255,255,255,0.5);
			}
			.blackbox-card-desc {
				color: rgba(255,255,255,0.7);
				font-size: 0.9rem;
				line-height: 1.5;
				flex-grow: 1;
				margin-bottom: 24px;
			}
			.blackbox-card-footer {
				display: flex;
				justify-content: space-between;
				align-items: center;
				border-top: 1px solid rgba(255,255,255,0.05);
				padding-top: 16px;
			}
			.blackbox-badge {
				padding: 4px 12px;
				border-radius: 20px;
				font-size: 0.75rem;
				font-weight: 600;
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			.badge-active {
				background: rgba(77, 250, 123, 0.1);
				color: #4dfa7b;
				border: 1px solid rgba(77, 250, 123, 0.2);
			}
			.badge-inactive {
				background: rgba(255, 255, 255, 0.05);
				color: rgba(255,255,255,0.4);
				border: 1px solid rgba(255,255,255,0.1);
			}
			.blackbox-actions {
				display: flex;
				gap: 8px;
			}
			.blackbox-actions a {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				padding: 6px 16px;
				border-radius: 4px;
				font-size: 0.85rem;
				font-weight: 600;
				letter-spacing: 0.5px;
				text-decoration: none;
				text-transform: uppercase;
				transition: all 0.2s ease;
			}
			.btn-go {
				background: rgba(98, 201, 255, 0.1);
				color: var(--accent, #62c9ff);
				border: 1px solid rgba(98, 201, 255, 0.3);
			}
			.btn-go:hover {
				background: rgba(98, 201, 255, 0.2);
				color: #fff;
				border-color: rgba(98, 201, 255, 0.6);
				box-shadow: 0 4px 15px rgba(98, 201, 255, 0.2);
				transform: translateY(-1px);
			}
			.btn-on {
				background: rgba(77, 250, 123, 0.05);
				color: #4dfa7b;
				border: 1px solid rgba(77, 250, 123, 0.3);
			}
			.btn-on:hover {
				background: rgba(77, 250, 123, 0.15);
				color: #fff;
				border-color: rgba(77, 250, 123, 0.6);
				box-shadow: 0 4px 15px rgba(77, 250, 123, 0.2);
				transform: translateY(-1px);
			}
			.btn-off {
				background: rgba(255, 107, 107, 0.05);
				color: #ff6b6b;
				border: 1px solid rgba(255, 107, 107, 0.3);
			}
			.btn-off:hover {
				background: rgba(255, 107, 107, 0.15);
				color: #fff;
				border-color: rgba(255, 107, 107, 0.6);
				box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
				transform: translateY(-1px);
			}
		</style>

		<div class="wrap blackbox-dashboard">
			<div class="blackbox-header">
				<img src="<?php echo esc_url( plugins_url( 'assets/images/obsidian.png', dirname( __DIR__ ) . '/BlackBOX.php' ) ); ?>" alt="BlackBOX">
				<div>
					<h1>BlackBOX Operations Matrix</h1>
					<p style="margin:0; color:rgba(255,255,255,0.6); font-size:1.1rem;">Centralized visualization of all primary engine and infrastructure plugins.</p>
				</div>
			</div>

			<div class="blackbox-grid">
				<?php foreach ( $display_plugins as $plugin ) : 
					$status_class = $plugin['active'] ? 'badge-active' : 'badge-inactive';
					$status_text = $plugin['active'] ? 'Active' : 'Inactive';
					$type_class = $plugin['type'] === 'Infrastructure' ? 'is-infrastructure' : 'is-compass';
				?>
					<div class="blackbox-card <?php echo $plugin['active'] ? 'is-active' : ''; ?> <?php echo $type_class; ?>">
						<div class="blackbox-card-header">
							<img src="<?php echo esc_url( $plugin['icon'] ); ?>" class="blackbox-card-icon" alt="Icon">
							<div class="blackbox-card-title-area">
								<h2><?php echo esc_html( $plugin['name'] ); ?></h2>
								<div class="blackbox-card-meta">
									<span>v<?php echo esc_html( $plugin['version'] ); ?></span>
									<span>&bull;</span>
									<span><?php echo esc_html( $plugin['type'] ); ?></span>
								</div>
							</div>
						</div>
						<div class="blackbox-card-desc">
							<?php echo wp_kses_post( $plugin['desc'] ); ?>
						</div>
						<div class="blackbox-card-footer">
							<span class="blackbox-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
							<div class="blackbox-actions">
								<?php if ( $plugin['active'] ) : ?>
									<a href="<?php echo esc_url( $plugin['go_url'] ); ?>" class="btn-go">Go</a>
									<a href="#" class="btn-off btn-toggle" data-action="deactivate" data-plugin="<?php echo esc_attr( $plugin['path'] ); ?>" data-go="<?php echo esc_attr( $plugin['go_url'] ); ?>">Off</a>
								<?php else : ?>
									<a href="#" class="btn-on btn-toggle" data-action="activate" data-plugin="<?php echo esc_attr( $plugin['path'] ); ?>" data-go="<?php echo esc_attr( $plugin['go_url'] ); ?>">On</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', () => {
			document.body.addEventListener('click', async (e) => {
				if (e.target.classList.contains('btn-toggle')) {
					e.preventDefault();
					const btn = e.target;
					const action = btn.dataset.action;
					const plugin = btn.dataset.plugin;
					const card = btn.closest('.blackbox-card');
					
					btn.style.opacity = '0.5';
					btn.innerText = '...';

					const formData = new URLSearchParams();
					formData.append('action', 'blackbox_toggle_plugin');
					formData.append('nonce', '<?php echo wp_create_nonce("blackbox_toggle"); ?>');
					formData.append('toggle', action);
					formData.append('plugin', plugin);

					try {
						const response = await fetch(ajaxurl, {
							method: 'POST',
							headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
							body: formData
						});
						
						const data = await response.json();
						
						if (data.success) {
							const goUrl = btn.dataset.go;
							const actions = card.querySelector('.blackbox-actions');
							
							if (action === 'activate') {
								card.classList.add('is-active');
								card.querySelector('.blackbox-badge').className = 'blackbox-badge badge-active';
								card.querySelector('.blackbox-badge').innerText = 'Active';
								actions.innerHTML = `
									<a href="${goUrl}" class="btn-go">Go</a>
									<a href="#" class="btn-off btn-toggle" data-action="deactivate" data-plugin="${plugin}" data-go="${goUrl}">Off</a>
								`;
							} else {
								card.classList.remove('is-active');
								card.querySelector('.blackbox-badge').className = 'blackbox-badge badge-inactive';
								card.querySelector('.blackbox-badge').innerText = 'Inactive';
								actions.innerHTML = `
									<a href="#" class="btn-on btn-toggle" data-action="activate" data-plugin="${plugin}" data-go="${goUrl}">On</a>
								`;
							}
						} else {
							alert('Error: ' + (data.data || 'Failed to toggle'));
							btn.style.opacity = '1';
							btn.innerText = action === 'activate' ? 'On' : 'Off';
						}
					} catch (err) {
						alert('Network Error');
						btn.style.opacity = '1';
						btn.innerText = action === 'activate' ? 'On' : 'Off';
					}
				}
			});
		});
		</script>
		<?php
	}

	public function group_wpmudev_plugins() {
		global $menu, $submenu;

		$wpmudev_slugs = [
			'wpmudev',
			'forminator',
			'wp-defender',
			'wphb',
			'smush',
			'smush-pro',
			'hustle',
			'beehive',
			'snapshot',
			'branding',
			'wds_wizard',
			'shipper',
			'wpmudev-videos',
			'blc_dash' // Broken Link Checker
		];

		if ( ! empty( $menu ) ) {
			foreach ( $menu as $key => $item ) {
				$slug = $item[2];
				
				if ( in_array( $slug, $wpmudev_slugs, true ) ) {
					// Strip tags to remove notification badges for the sidebar menu title
					$title = wp_strip_all_tags( $item[0] );
					self::$grouped_wpmudev_names[ $slug ] = $title;
					
					// Store the customized icon registered by the plugin (if it's a URL or base64 data)
					if ( isset( $item[6] ) && ! empty( $item[6] ) ) {
						// Filter out dashicons classes, keeping only true image URLs/data URIs
						if ( strpos( $item[6], 'http' ) === 0 || strpos( $item[6], 'data:image' ) === 0 || strpos( $item[6], '/' ) === 0 ) {
							self::$grouped_wpmudev_icons[ $slug ] = $item[6];
						}
					}

					// Move to BlackBOX submenu
					$submenu['blackbox-plugins'][] = [
						$title,
						$item[1],
						$slug,
						$item[3] ?? $title
					];

					// Remove from main menu
					unset( $menu[$key] );

					// Note: We leave the submenus in $submenu[$slug] intact so that if the user 
					// navigates directly to a sub-page, WordPress still recognizes it as a valid page callback.
				}
			}
		}
	}

	public function ajax_toggle_plugin() {
		check_ajax_referer( 'blackbox_toggle', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';
		$action = isset( $_POST['toggle'] ) ? sanitize_text_field( wp_unslash( $_POST['toggle'] ) ) : '';

		if ( empty( $plugin ) || ! in_array( $action, [ 'activate', 'deactivate' ], true ) ) {
			wp_send_json_error( 'Invalid parameters' );
		}

		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( $action === 'activate' ) {
			$result = activate_plugin( $plugin );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result->get_error_message() );
			}
		} else {
			deactivate_plugins( $plugin );
		}

		wp_send_json_success( [
			'status' => $action,
			'plugin' => $plugin
		] );
	}

	public function settings_page_display() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap" style="position: relative; min-height: 80vh;">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post" style="position: relative; z-index: 2;">
				<?php
				settings_fields( 'xophz_compass_options_group' );
				do_settings_sections( 'xophz_compass_settings' );
				submit_button( 'Save Configuration' );
				?>
			</form>

			<div style="position: fixed; bottom: 20px; right: 20px; opacity: 0.05; pointer-events: none; z-index: 1;">
				<img src="<?php echo esc_url( content_url( 'mu-plugins/blackbox-bedrock/assets/images/hallofthegodsinc.png' ) ); ?>" alt="Hall of the Gods Logo" style="width: 300px; height: auto;" />
			</div>
		</div>
		<?php
	}

	public function register_settings() {
		register_setting( 'xophz_compass_options_group', 'xophz_compass_disable_mu_styles' );

		add_settings_section(
			'xophz_compass_general_section',
			'w⁴ Protocol Configuration',
			function() {
				echo '<p>Manage w⁴ Protocol overrides and core visual matrix toggles.</p>';
			},
			'xophz_compass_settings'
		);

		add_settings_field(
			'xophz_compass_disable_mu_styles',
			'Disable UI Matrix',
			function() {
				$val = get_option( 'xophz_compass_disable_mu_styles', '0' );
				echo '<label><input type="checkbox" name="xophz_compass_disable_mu_styles" value="1" ' . checked( 1, $val, false ) . ' /> Check to bypass the glassmorphic rendering and restore the standard WordPress admin interface.</label>';
			},
			'xophz_compass_settings',
			'xophz_compass_general_section'
		);
	}

	public function output_theme_colors() {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) {
			return;
		}

		global $_wp_admin_css_colors;
		$user_id = get_current_user_id();
		$color_scheme = get_user_option( 'admin_color', $user_id );
		
		// Map of WordPress Core standard admin color palettes
		$core_palettes = [
			'fresh'     => ['#1d2327', '#2c3338', '#2271b1', '#72aee6'],
			'light'     => ['#e5e5e5', '#999999', '#d64e07', '#04a4cc'],
			'blue'      => ['#096484', '#4796b3', '#52accc', '#74B6CE'],
			'midnight'  => ['#25282b', '#363b3f', '#69a8bb', '#e14d43'],
			'spice'     => ['#46403c', '#59524c', '#c7a589', '#9ea476'],
			'coffee'    => ['#46403c', '#59524c', '#c7a589', '#9ea476'], // spice fallback
			'ocean'     => ['#627c83', '#738e96', '#9ebaa0', '#aa9d88'],
			'sunrise'   => ['#b43c38', '#cf4944', '#dd823b', '#ccaf0b'],
			'ectoplasm' => ['#413256', '#523f6d', '#a3b745', '#d46f15'],
		];
		
		$colors = $core_palettes['fresh']; 
		
		if (isset($_wp_admin_css_colors[$color_scheme])) {
			$colors = $_wp_admin_css_colors[$color_scheme]->colors;
		} elseif (isset($core_palettes[$color_scheme])) {
			$colors = $core_palettes[$color_scheme];
		}

		$c0 = $colors[0] ?? '#1d2327';
		$c1 = $colors[1] ?? '#2c3338';
		$c2 = $colors[2] ?? '#2271b1';
		$c3 = $colors[3] ?? '#72aee6';

		$active = $c2;
		if ($color_scheme === 'light') {
			$active = $c1;
		}
		
		echo "<style id=\"blackbox-theme-colors\">
			:root { 
				--wp-theme-base: {$c0}; 
				--wp-theme-focus: {$c1}; 
				--wp-theme-color: {$c2}; 
				--wp-theme-secondary: {$c3}; 
				--wp-theme-active: {$active};
			}
			body {
				--wp-active-scheme: {$color_scheme};
			}
		</style>";
	}

	public function enqueue_styles( $return = false ) {
		// Because WordPress hooks can pass string arguments...
		if ( ! is_bool( $return ) ) {
			$return = false;
		}

		// Do not apply blackbox styling to the plugin information popup standard WP iframe
		if ( isset( $_GET['tab'] ) && $_GET['tab'] === 'plugin-information' ) {
			return;
		}

		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) {
			return $return ? '' : null;
		}

		static $done = false;
		if ( $done && ! $return && current_action() !== 'enqueue_block_editor_assets' ) return;
		if ( ! $return ) $done = true;

		$isIframe = (isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1') || 
		            (isset( $_SERVER['HTTP_SEC_FETCH_DEST'] ) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe');
		$isInstalling = defined( 'WP_INSTALLING' ) && WP_INSTALLING;

		$styles = [ 'logo.css', 'sui.css', 'base.css', 'wp-admin.css', 'iframe-mask.css' ];
		if ( $isInstalling || $return ) {
			array_unshift( $styles, 'install.css' );
		}

		$global_css = '';
		foreach ( array_unique($styles) as $style ) {
			$path = dirname( __DIR__ ) . '/assets/css/' . $style;
			if ( file_exists( $path ) ) {
				$global_css .= file_get_contents( $path );
			}
		}

		// Auto-load section-specific styles for code splitting
		$sections_dir = dirname( __DIR__ ) . '/assets/css/sections/';
		if ( is_dir( $sections_dir ) ) {
			foreach ( glob( $sections_dir . '*.css' ) as $file ) {
				$global_css .= file_get_contents( $file );
			}
		}

		if ( $return ) return $global_css;

		if ( current_action() === 'enqueue_block_editor_assets' ) {
			$gutenberg_path = dirname( __DIR__ ) . '/assets/css/gutenberg.css';
			$gutenberg_css = file_exists( $gutenberg_path ) ? file_get_contents( $gutenberg_path ) : '';
			$editor_css = $global_css . $gutenberg_css;
			wp_add_inline_style( 'wp-block-library', $editor_css );
			wp_add_inline_style( 'wp-edit-post', $editor_css );
		} else {
			echo '<style id="blackbox-global-admin">' . $global_css . '</style>';
		}
	}

	public function inject_into_install_tag( $tag, $handle ) {
		if ( $handle === 'install' ) {
			$tag .= '<style id="blackbox-global-install">' . $this->enqueue_styles( true ) . '</style>';
		}
		return $tag;
	}

	public function inject_into_install_scripts( $tag, $handle ) {
		if ( $handle === 'language-chooser' ) {
			ob_start();
			Core::inject_canvas_script();
			$tag .= ob_get_clean();
		}
		return $tag;
	}

	public function inject_iframe_class() {
		// Do not apply blackbox styling to the plugin information popup standard WP iframe
		if ( isset( $_GET['tab'] ) && $_GET['tab'] === 'plugin-information' ) {
			return;
		}

		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) {
			return;
		}

		$isIframe = (isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1') || 
		            (isset( $_SERVER['HTTP_SEC_FETCH_DEST'] ) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe');

		if ( $isIframe ) {
			echo '<script>document.documentElement.classList.add("is-blackbox-iframe", "is-compass-iframe");</script>';
		} else {
			echo '<script>if (window.name === "blackbox-sub-app" || window.name === "compass-sub-app") { document.documentElement.classList.add("is-blackbox-iframe", "is-compass-iframe"); }</script>';
		}
	}

	public function override_editor_theme_json( $theme_json ) {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) {
			return $theme_json;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! $screen || ! $screen->is_block_editor() ) {
				return $theme_json;
			}
		}

		$new_data = [
			'version' => 2,
			'styles' => [
				'color' => [ 'background' => 'transparent', 'text' => '#f8f8f2' ],
				'elements' => [
					'link' => [ 'color' => [ 'text' => '#62c9ff' ] ],
					'heading' => [ 'color' => [ 'text' => '#f8f8f2' ] ]
				]
			]
		];
		return $theme_json->update_with( $new_data );
	}

	public function force_editor_css_settings( $settings, $context ) {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) {
			return $settings;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! $screen || ! $screen->is_block_editor() ) {
				return $settings;
			}
		}

		$bg_canvas = 'transparent';
		$custom_css = '
			.editor-styles-wrapper, .is-root-container, .block-editor-writing-flow, body, html {
				background: ' . $bg_canvas . ' !important;
				background-color: ' . $bg_canvas . ' !important;
				color: #f8f8f2 !important;
			}
			.wp-block { color: #f8f8f2 !important; }
		';

		if ( ! isset( $settings['styles'] ) ) {
			$settings['styles'] = [];
		}
		$settings['styles'][] = [ 'css' => $custom_css ];
		return $settings;
	}

	public function add_classic_editor_dark_css( $mce_css ) {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) {
			return $mce_css;
		}

		$css_url = plugins_url( 'assets/css/tinymce-content.css', dirname( __FILE__ ) );
		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}
		$mce_css .= $css_url;
		return $mce_css;
	}
}
