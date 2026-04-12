<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin {
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'admin_print_styles', [ $this, 'enqueue_styles' ], 9999 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_styles' ], 9999 );
		add_filter( 'wp_theme_json_data_theme', [ $this, 'override_editor_theme_json' ] );
		add_filter( 'style_loader_tag', [ $this, 'inject_into_install_tag' ], 9999, 2 );
		add_filter( 'script_loader_tag', [ $this, 'inject_into_install_scripts' ], 9999, 2 );
		add_action( 'admin_head', [ $this, 'inject_iframe_class' ], 1 );
		
		// Use admin_footer for broader compatibility in minimal screens
		add_action( 'admin_footer', [ Core::class, 'inject_canvas_script' ], 9999 );

		if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
			// Force inject on install.php if footer hook doesn't fire early enough
			add_action( 'admin_head', [ Core::class, 'inject_canvas_script' ], 9999 );
		}
	}

	public function enqueue_styles( $return = false ) {
		$styles = [ 'logo.css', 'install.css', 'sui.css', 'base.css', 'wp-admin.css', 'iframe-mask.css' ];
		$global_css = '';
		foreach ( $styles as $style ) {
			$path = dirname( __DIR__ ) . '/css/' . $style;
			if ( file_exists( $path ) ) {
				$global_css .= file_get_contents( $path );
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
		$isIframe = isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1';
		if ( $isIframe || ( isset( $_SERVER['HTTP_SEC_FETCH_DEST'] ) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe' ) ) {
			echo '<script>document.documentElement.classList.add("is-blackbox-iframe");</script>';
		}
	}

	public function override_editor_theme_json( $theme_json ) {
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
}
