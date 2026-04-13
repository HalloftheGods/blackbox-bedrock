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
			$path = dirname( __DIR__ ) . '/css/' . $style;
			if ( file_exists( $path ) ) {
				$global_css .= file_get_contents( $path );
			}
		}

		// Auto-load section-specific styles for code splitting
		$sections_dir = dirname( __DIR__ ) . '/css/sections/';
		if ( is_dir( $sections_dir ) ) {
			foreach ( glob( $sections_dir . '*.css' ) as $file ) {
				$global_css .= file_get_contents( $file );
			}
		}

		if ( $return ) return $global_css;

		if ( current_action() === 'enqueue_block_editor_assets' ) {
			$gutenberg_path = dirname( __DIR__ ) . '/css/gutenberg.css';
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
