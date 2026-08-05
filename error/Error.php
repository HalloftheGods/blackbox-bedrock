<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class Error {
	public function __construct() {
		add_filter( 'wp_die_handler', [ $this, 'blackbox_die_handler' ], 999 );
		add_filter( 'wp_php_error_message', [ $this, 'blackbox_error_message' ], 999, 2 );
	}

	public function blackbox_die_handler() {
		return [ $this, 'render_error_template' ];
	}

	public function blackbox_error_message( $message, $error ) {
		return 'We are currently undergoing scheduled maintenance. Please check back shortly.';
	}

	public function render_error_template( $message, $title = '', $args = [] ) {
		if ( empty( $title ) ) {
			$title = 'Scheduled Maintenance';
		}
		include __DIR__ . '/error-template.php';
		die();
	}
}
