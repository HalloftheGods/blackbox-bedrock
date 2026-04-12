<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class Error {
	public function __construct() {
		add_filter( 'wp_die_handler', [ $this, 'blackbox_die_handler' ] );
	}

	public function blackbox_die_handler() {
		return [ $this, 'render_error_template' ];
	}

	public function render_error_template( $message, $title = '', $args = [] ) {
		if ( empty( $title ) ) {
			$title = 'COMPASS Critical Error';
		}
		// $message and $title will be available in the included file
		include __DIR__ . '/error-template.php';
		die();
	}
}
