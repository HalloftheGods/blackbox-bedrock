<?php
namespace BlackBOX\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Editor_Support {

	public function __construct() {
		if ( ! empty( get_option( 'blackbox_bedrock_disabled' ) ) ) {
			return;
		}

		add_filter( 'block_editor_settings_all', [ $this, 'force_editor_css_settings' ], 9999, 2 );
		add_filter( 'mce_css', [ $this, 'add_classic_editor_dark_css' ] );
		add_filter( 'wp_theme_json_data_theme', [ $this, 'override_editor_theme_json' ] );
	}

	public function override_editor_theme_json( $theme_json ) {
		if ( ! empty( get_option( 'xophz_compass_disable_mu_styles' ) ) ) {
			return $theme_json;
		}

		global $pagenow;
		if ( $pagenow === 'customize.php' || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
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

		global $pagenow;
		if ( $pagenow === 'customize.php' || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
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

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$css_url = $assets_url . '/css/tinymce-content.css';
		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}
		$mce_css .= $css_url;
		return $mce_css;
	}
}
