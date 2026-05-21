<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class Core {
	public static function inject_canvas_script() {
		$js_path = dirname( __DIR__ ) . '/assets/js/smoke-canvas.js';
		$js = file_exists( $js_path ) ? file_get_contents( $js_path ) : '';
		echo '<script id="blackbox-smoke-canvas-js">' . $js . '</script>';
	}

	public static function force_woocommerce_theme_support() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}

add_action( 'after_setup_theme', [ '\BlackBOX\Core', 'force_woocommerce_theme_support' ], 999 );
