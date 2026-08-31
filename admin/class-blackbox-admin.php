<?php
namespace BlackBOX;

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/class-admin-settings.php';
require_once __DIR__ . '/class-admin-dashboard.php';
require_once __DIR__ . '/class-admin-theme-styler.php';
require_once __DIR__ . '/class-admin-editor-support.php';
require_once __DIR__ . '/class-admin-menu-manager.php';

class Admin {

	/**
	 * @var Admin\Settings
	 */
	public $settings;

	/**
	 * @var Admin\Dashboard
	 */
	public $dashboard;

	/**
	 * @var Admin\Theme_Styler
	 */
	public $theme_styler;

	/**
	 * @var Admin\Editor_Support
	 */
	public $editor_support;

	/**
	 * @var Admin\Menu_Manager
	 */
	public $menu_manager;

	public function __construct() {
		$this->settings       = new Admin\Settings();
		$this->dashboard      = new Admin\Dashboard();
		$this->theme_styler   = new Admin\Theme_Styler();
		$this->editor_support = new Admin\Editor_Support();
		$this->menu_manager   = new Admin\Menu_Manager( $this->dashboard, $this->settings );
	}

	// ----------------------------------------------------------------------
	// Backward-Compatibility Proxies
	// ----------------------------------------------------------------------

	public function register_w4_protocol_menu() {
		$this->menu_manager->register_w4_protocol_menu();
	}

	public function register_blackbox_menu() {
		$this->menu_manager->register_blackbox_menu();
	}

	public function render_blackbox_page() {
		$this->dashboard->render_blackbox_page();
	}

	public function ajax_toggle_plugin() {
		$this->dashboard->ajax_toggle_plugin();
	}

	public function settings_page_display() {
		$this->settings->settings_page_display();
	}

	public function register_settings() {
		$this->settings->register_settings();
	}

	public function output_theme_colors() {
		$this->theme_styler->output_theme_colors();
	}

	public function enqueue_styles( $return = false ) {
		return $this->theme_styler->enqueue_styles( $return );
	}

	public function inject_into_install_tag( $tag, $handle ) {
		return $this->theme_styler->inject_into_install_tag( $tag, $handle );
	}

	public function inject_into_install_scripts( $tag, $handle ) {
		return $this->theme_styler->inject_into_install_scripts( $tag, $handle );
	}

	public function inject_iframe_class() {
		$this->theme_styler->inject_iframe_class();
	}

	public function override_editor_theme_json( $theme_json ) {
		return $this->editor_support->override_editor_theme_json( $theme_json );
	}

	public function force_editor_css_settings( $settings, $context ) {
		return $this->editor_support->force_editor_css_settings( $settings, $context );
	}

	public function add_classic_editor_dark_css( $mce_css ) {
		return $this->editor_support->add_classic_editor_dark_css( $mce_css );
	}

	public function inject_admin_canvas() {
		$this->theme_styler->inject_admin_canvas();
	}

	public function output_modal_footer_overrides() {
		$this->theme_styler->output_modal_footer_overrides();
	}

	public function early_compass_isolation() {
		$this->theme_styler->early_compass_isolation();
	}

	public function prevent_menu_cls() {
		$this->menu_manager->prevent_menu_cls();
	}

	public function group_wpmudev_plugins() {
		$this->menu_manager->group_wpmudev_plugins();
	}

	public function build_compass_group_map() {
		return $this->menu_manager->build_compass_group_map();
	}

	public function output_accordion_js() {
		$this->menu_manager->output_accordion_js();
	}
}
