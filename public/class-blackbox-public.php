<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BlackBOX_Public {

	public function __construct() {
		add_action( 'login_enqueue_scripts', [ $this, 'enqueue_login_styles' ] );
		add_filter( 'login_headerurl', [ $this, 'custom_login_headerurl' ] );
		add_filter( 'login_headertext', [ $this, 'custom_login_headertext' ] );
		add_action( 'login_footer', [ 'BlackBOX_Core', 'inject_canvas_script' ], 9999 );
	}

	public function enqueue_login_styles() {
		$plugin_dir = plugin_dir_path( dirname( __FILE__ ) );
		$logo_css = file_get_contents( $plugin_dir . 'css/logo.css' );
		$install_css = file_get_contents( $plugin_dir . 'css/install.css' );
		$login_css = file_get_contents( $plugin_dir . 'css/login.css' );
		echo '<style id="blackbox-login-admin">' . $logo_css . $install_css . $login_css . '</style>';
	}

	public function custom_login_headerurl() {
		return home_url();
	}

	public function custom_login_headertext() {
		return 'COMPASS Suite';
	}
}
