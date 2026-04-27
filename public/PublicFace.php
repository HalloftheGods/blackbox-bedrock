<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class PublicFace {
	public function __construct() {
		add_action( 'login_enqueue_scripts', [ $this, 'enqueue_login_styles' ] );
		add_filter( 'login_headerurl', [ $this, 'custom_login_headerurl' ] );
		add_filter( 'login_headertext', [ $this, 'custom_login_headertext' ] );
		add_action( 'login_footer', [ '\BlackBOX\Core', 'inject_canvas_script' ], 9999 );
	}

	public function enqueue_login_styles() {
		$logo_path = dirname( __DIR__ ) . '/assets/css/logo.css';
		$login_path = dirname( __DIR__ ) . '/assets/css/login.css';
		
		$logo_css = file_exists( $logo_path ) ? file_get_contents( $logo_path ) : '';
		$login_css = file_exists( $login_path ) ? file_get_contents( $login_path ) : '';
		
		$assets_url = defined('WPMU_PLUGIN_URL') ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url('mu-plugins/blackbox-bedrock/assets');
		$logo_css = str_replace( '../images/', $assets_url . '/images/', $logo_css );
		$login_css = str_replace( '../images/', $assets_url . '/images/', $login_css );
		
		echo '<style id="blackbox-login-admin">' . $logo_css . $login_css . '</style>';
	}

	public function custom_login_headerurl() {
		return home_url();
	}

	public function custom_login_headertext() {
		return 'COMPASS Suite';
	}
}
