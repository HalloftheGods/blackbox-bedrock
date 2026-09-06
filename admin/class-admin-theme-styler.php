<?php
namespace BlackBOX\Admin;

use BlackBOX\Core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Theme_Styler {

	public function __construct() {
		if ( ! empty( get_option( 'blackbox_bedrock_disabled' ) ) || ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_print_styles', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'early_compass_isolation' ], 0 );
		add_action( 'admin_head', [ $this, 'inject_iframe_class' ], 1 );
		add_action( 'admin_head', [ $this, 'output_theme_colors' ], 15 );
		add_action( 'admin_footer', [ $this, 'inject_admin_canvas' ], 9999 );
		add_action( 'admin_enqueue_scripts', [ $this, 'output_modal_footer_overrides' ], 9999 );
		add_filter( 'script_loader_tag', [ $this, 'inject_into_install_scripts' ], 9999, 2 );
		add_filter( 'style_loader_tag', [ $this, 'inject_into_install_tag' ], 9999, 2 );

		if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
			// Force inject on install.php if footer hook doesn't fire early enough
			add_action( 'admin_head', [ $this, 'inject_admin_canvas' ], 9999 );
			add_action( 'admin_head', function() {
				if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) return;
				echo '<script>document.addEventListener("DOMContentLoaded", function() { document.body.classList.add("body-glass"); });</script>';
			}, 9999 );
		}
	}

	public function early_compass_isolation() {
		$is_compass = ( isset( $_GET['page'] ) && ( $_GET['page'] === 'xophz-compass' || $_GET['page'] === 'youmeos' ) );
		if ( ! $is_compass ) {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			if ( $screen && ( $screen->id === 'toplevel_page_xophz-compass' || $screen->id === 'toplevel_page_youmeos' ) ) {
				$is_compass = true;
			}
		}

		if ( $is_compass ) {
			?>
			<style id="compass-early-isolation">
				/* Instant Pre-Render Isolation: Hides WP Chrome & Sidebar before Vue mounts to eliminate FOUC */
				#wpadminbar,
				#adminmenuback,
				#adminmenuwrap,
				#adminmenumain,
				#wpfooter,
				.update-nag,
				.notice:not(.compass-notice),
				#wpbody-content > .notice {
					display: none !important;
				}
				html,
				html.wp-toolbar {
					padding-top: 0 !important;
					overflow: hidden !important;
				}
				body,
				body.admin-bar {
					margin-top: 0 !important;
				}
				#wpcontent,
				#wpbody,
				#wpbody-content {
					padding: 0 !important;
					margin: 0 !important;
					margin-left: 0 !important;
				}
				body > #app {
					top: 0 !important;
					left: 0 !important;
					right: 0 !important;
					bottom: 0 !important;
				}
				body.wp-responsive-open #adminmenuwrap,
				body.wp-responsive-open #adminmenuback,
				body.wp-responsive-open #adminmenumain {
					display: block !important;
				}
				body.wp-responsive-open > #app {
					left: 160px !important;
				}
				body.folded.wp-responsive-open > #app {
					left: 36px !important;
				}
			</style>
			<?php
		}
	}

	public function output_theme_colors() {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) || ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ) {
			return;
		}

		global $pagenow;
		if ( $pagenow === 'customize.php' || $pagenow === 'site-editor.php' || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && ( $screen->id === 'site-editor' || $screen->base === 'site-editor' ) ) {
				return;
			}
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

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$assets_dir = dirname( __DIR__ ) . '/assets';

		wp_enqueue_script(
			'blackbox-theme-colors',
			$assets_url . '/js/theme-colors.js',
			[],
			filemtime( $assets_dir . '/js/theme-colors.js' ),
			true
		);
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

		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) || ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ) {
			return $return ? '' : null;
		}

		global $pagenow;
		if ( $pagenow === 'customize.php' || $pagenow === 'site-editor.php' || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return $return ? '' : null;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && ( $screen->id === 'site-editor' || $screen->base === 'site-editor' ) ) {
				return $return ? '' : null;
			}
		}

		static $done = false;
		if ( $done && ! $return && current_action() !== 'enqueue_block_editor_assets' ) return;
		if ( ! $return ) $done = true;

		$isIframe = (isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1') || 
		            (isset( $_SERVER['HTTP_SEC_FETCH_DEST'] ) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe');
		$isInstalling = defined( 'WP_INSTALLING' ) && WP_INSTALLING;

		$styles = [ 'logo.css', 'sui.css', 'base.css', 'wp-admin.css', 'iframe-mask.css', 'menu-accordion.css' ];
		if ( $isInstalling || $return ) {
			array_unshift( $styles, 'install.css' );
		}

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );

		$global_css = '';
		foreach ( array_unique($styles) as $style ) {
			$path = dirname( __DIR__ ) . '/assets/css/' . $style;
			if ( file_exists( $path ) ) {
				$css_content = file_get_contents( $path );
				$css_content = str_replace( '../images/', $assets_url . '/images/', $css_content );
				$global_css .= $css_content;
			}
		}

		// Auto-load section-specific styles for code splitting
		$sections_dir = dirname( __DIR__ ) . '/assets/css/sections/';
		if ( is_dir( $sections_dir ) ) {
			foreach ( glob( $sections_dir . '*.css' ) as $file ) {
				$css_content = file_get_contents( $file );
				$css_content = str_replace( '../images/', $assets_url . '/images/', $css_content );
				$global_css .= $css_content;
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
			ob_start();
			Core::inject_canvas_script();
			$tag .= ob_get_clean();
			$tag .= '<script>
			document.addEventListener("DOMContentLoaded", function() {
				document.body.classList.add("wp-admin-install", "body-glass");
				var oldLogo = document.getElementById("logo");
				if (oldLogo) {
					oldLogo.remove();
				}

				if (!document.getElementById("install-layout-wrapper")) {
					var wrapper = document.createElement("div");
					wrapper.id = "install-layout-wrapper";

					var logoCol = document.createElement("div");
					logoCol.id = "install-logo-col";
					logoCol.innerHTML = \'<div class="brand-hero">\' +
						\'<div class="brand-duo">\' +
							\'<div class="logo-omega-mark"></div>\' +
							\'<div class="brand-plus">+</div>\' +
							\'<div class="logo-wp-mark">\' +
								\'<span class="dashicons dashicons-wordpress-alt wp-icon-glyph"></span>\' +
							\'</div>\' +
						\'</div>\' +
						\'<div class="brand-text-block">\' +
							\'<h2 class="brand-heading">YouMeOS <span class="gold-gradient-text">Microverse</span></h2>\' +
							\'<p class="brand-caption">Self-Contained Local Web Universe</p>\' +
						\'</div>\' +
					\'</div>\';
					wrapper.appendChild(logoCol);

					var cardCol = document.createElement("div");
					cardCol.id = "install-card-col";
					var children = Array.from(document.body.children).filter(function(el) {
						return el !== wrapper && el.id !== "logo" && el.tagName !== "CANVAS" && el.tagName !== "SCRIPT" && el.tagName !== "STYLE";
					});
					children.forEach(function(child) { cardCol.appendChild(child); });
					wrapper.appendChild(cardCol);

					document.body.appendChild(wrapper);

					// Pre-populate sensible defaults if empty
					var titleInput = document.getElementById("weblog_title");
					if (titleInput && !titleInput.value) {
						titleInput.value = "My Microverse";
					}

					var userInput = document.getElementById("user_login");
					if (userInput && !userInput.value) {
						userInput.value = "";
					}

					var emailInput = document.getElementById("admin_email");
					if (emailInput && !emailInput.value) {
						emailInput.value = "";
					}
				}
			});
			</script>';
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

		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) || ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ) {
			return;
		}

		global $pagenow;
		if ( $pagenow === 'customize.php' || $pagenow === 'site-editor.php' || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && ( $screen->id === 'site-editor' || $screen->base === 'site-editor' ) ) {
				return;
			}
		}

		$isIframe = (isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1') || 
		            (isset( $_SERVER['HTTP_SEC_FETCH_DEST'] ) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe');

		if ( $isIframe ) {
			echo '<script>document.documentElement.classList.add("is-blackbox-iframe", "is-compass-iframe");</script>';
		} else {
			echo '<script>if (window.name === "blackbox-sub-app" || window.name === "compass-sub-app") { document.documentElement.classList.add("is-blackbox-iframe", "is-compass-iframe"); }</script>';
		}
	}

	public function inject_admin_canvas() {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) || ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ) {
			return;
		}

		global $pagenow;
		if ( $pagenow === 'customize.php' || $pagenow === 'site-editor.php' || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && ( $screen->id === 'site-editor' || $screen->base === 'site-editor' ) ) {
				return;
			}
		}

		Core::inject_canvas_script();
	}

	public function output_modal_footer_overrides() {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) || ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ) {
			return;
		}

		global $pagenow;
		if ( $pagenow === 'customize.php' || $pagenow === 'site-editor.php' || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && ( $screen->id === 'site-editor' || $screen->base === 'site-editor' ) ) {
				return;
			}
		}

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$assets_dir = dirname( __DIR__ ) . '/assets';

		wp_enqueue_style(
			'blackbox-modal-overrides',
			$assets_url . '/css/modal-overrides.css',
			[],
			filemtime( $assets_dir . '/css/modal-overrides.css' )
		);
	}
}
