<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class Core {
	public static function inject_canvas_script() {
		$js_path = dirname( __DIR__ ) . '/assets/js/smoke-canvas.js';
		$js = file_exists( $js_path ) ? file_get_contents( $js_path ) : '';
		echo '<script id="blackbox-smoke-canvas-js">' . $js . '</script>';
	}
}
