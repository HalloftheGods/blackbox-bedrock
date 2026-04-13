<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin {
	public function __construct() {
		add_filter( 'wp_theme_json_data_theme', [ $this, 'override_editor_theme_json' ] );
		add_filter( 'block_editor_settings_all', [ $this, 'force_editor_css_settings' ], 9999, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_print_styles', [ $this, 'enqueue_styles' ], 9999 );
		add_filter( 'style_loader_tag', [ $this, 'inject_into_install_tag' ], 9999, 2 );
		add_filter( 'script_loader_tag', [ $this, 'inject_into_install_scripts' ], 9999, 2 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'inject_iframe_class' ], 1 );

		if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
			// Force inject on install.php if footer hook doesn't fire early enough
			add_action( 'admin_head', [ Core::class, 'inject_canvas_script' ], 9999 );
			add_action( 'admin_head', function() {
				echo '<script>document.addEventListener("DOMContentLoaded", function() { document.body.classList.add("body-glass"); });</script>';
			}, 9999 );
		}

		add_action( 'admin_head', [ $this, 'output_theme_colors' ], 15 );
	}

	public function output_theme_colors() {
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

		$isIframe = (isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1') || 
		            (isset( $_SERVER['HTTP_SEC_FETCH_DEST'] ) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe');

		if ( $isIframe ) {
			echo '<script>document.documentElement.classList.add("is-blackbox-iframe", "is-compass-iframe");</script>';
		} else {
			echo '<script>if (window.name === "blackbox-sub-app" || window.name === "compass-sub-app") { document.documentElement.classList.add("is-blackbox-iframe", "is-compass-iframe"); }</script>';
		}
	}

	public function override_editor_theme_json( $theme_json ) {
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
}
