<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BlackBOX_Admin {

	public function __construct() {
		add_filter( 'wp_theme_json_data_theme', [ $this, 'override_editor_theme_json' ] );
		add_filter( 'block_editor_settings_all', [ $this, 'force_editor_css_settings' ], 9999, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_blackbox_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'enqueue_blackbox_styles' ], 9999 );
		add_action( 'admin_print_styles', [ $this, 'enqueue_blackbox_styles' ], 9999 );
		add_filter( 'style_loader_tag', [ $this, 'inject_into_install_tag' ], 9999, 2 );
		add_filter( 'script_loader_tag', [ $this, 'inject_into_install_scripts' ], 9999, 2 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_blackbox_styles' ], 9999 );
		add_action( 'admin_print_footer_scripts', [ 'BlackBOX_Core', 'inject_canvas_script' ], 9999 );
		add_action( 'admin_head', [ $this, 'inject_iframe_class' ], 1 );

		if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
			add_action( 'admin_print_styles', [ $this, 'enqueue_blackbox_styles' ], 9999 );
			add_action( 'admin_head', [ $this, 'enqueue_blackbox_styles' ], 9999 );
			add_action( 'admin_head', function() {
				echo '<script>document.addEventListener("DOMContentLoaded", function() { document.body.classList.add("body-glass"); });</script>';
			}, 9999 );
		}
	}

	public function inject_into_install_tag( $tag, $handle ) {
		if ( $handle === 'install' ) {
			$global_css = $this->enqueue_blackbox_styles( true );
			$tag .= '<style id="blackbox-global-install">' . $global_css . '</style>';
		}
		return $tag;
	}

	public function inject_into_install_scripts( $tag, $handle ) {
		if ( $handle === 'language-chooser' ) {
			ob_start();
			BlackBOX_Core::inject_canvas_script();
			$tag .= ob_get_clean();
		}
		return $tag;
	}

	public function inject_iframe_class() {
		$isIframe = isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1';
		if ( $isIframe ) {
			echo '<script>document.documentElement.classList.add("is-blackbox-iframe");</script>';
		} else {
			echo '<script>if (window.name === "blackbox-sub-app") { document.documentElement.classList.add("is-blackbox-iframe"); }</script>';
		}
	}

	public function override_editor_theme_json( $theme_json ) {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! $screen || ! $screen->is_block_editor() ) {
				return $theme_json;
			}
		}

		$bg_canvas = 'transparent';
		$text_main = '#f8f8f2';
		
		$new_data = array(
			'version' => 2,
			'styles' => array(
				'color' => array(
					'background' => $bg_canvas,
					'text'       => $text_main
				),
				'elements' => array(
					'link' => array(
						'color' => array( 'text' => '#62c9ff' )
					),
					'heading' => array(
						'color' => array( 'text' => $text_main )
					)
				)
			)
		);
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
			$settings['styles'] = array();
		}
		$settings['styles'][] = array( 'css' => $custom_css );
		return $settings;
	}

	public function enqueue_blackbox_styles( $return = false ) {
		$plugin_dir = plugin_dir_path( dirname( __FILE__ ) );
		$logo_css = file_get_contents( $plugin_dir . 'css/logo.css' );
		$install_css = file_get_contents( $plugin_dir . 'css/install.css' );
		$sui_css = file_get_contents( $plugin_dir . 'css/sui.css' );
		$base_css = file_get_contents( $plugin_dir . 'css/base.css' );
		$wp_admin_css = file_get_contents( $plugin_dir . 'css/wp-admin.css' );
		$iframe_css = file_get_contents( $plugin_dir . 'css/iframe-mask.css' );
		
		$global_css = $logo_css . $install_css . $sui_css . $base_css . $wp_admin_css . $iframe_css;

		if ( $return ) {
			return $global_css;
		}

		if ( current_action() === 'enqueue_block_editor_assets' ) {
			$gutenberg_css = file_get_contents( $plugin_dir . 'css/gutenberg.css' );
			$editor_css = $global_css . $gutenberg_css;
			wp_add_inline_style( 'wp-block-library', $editor_css );
			wp_add_inline_style( 'wp-edit-post', $editor_css );
		} else {
			echo '<style id="blackbox-global-admin">' . $global_css . '</style>';
		}
	}
}
