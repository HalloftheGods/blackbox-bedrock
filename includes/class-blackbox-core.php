<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BlackBOX_Core {

	public function __construct() {
		// Error handlers
		// Disabled wp_die_handler entirely as it may be wrapping normal WordPress outputs (like the install form) in error-template.php
		// if ( ! ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) ) {
		// 	add_filter( 'wp_die_handler', [ $this, 'blackbox_die_handler' ] );
		// }
	}

	public static function inject_canvas_script() {
		$js = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'assets/js/smoke-canvas.js' );
		echo '<script id="blackbox-smoke-canvas-js">' . $js . '</script>';
	}

	public function blackbox_die_handler() {
		return [ $this, 'render_error_template' ];
	}

	public function render_error_template( $message, $title = '', $args = [] ) {
		if ( empty( $title ) ) {
			$title = __( 'COMPASS Critical Error' );
		}
		include plugin_dir_path( dirname( __FILE__ ) ) . 'error-template.php';
		die();
	}
}
