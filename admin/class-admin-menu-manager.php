<?php
namespace BlackBOX\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Menu_Manager {

	private static $grouped_wpmudev_icons = [];
	private static $grouped_wpmudev_names = [];
	private $dashboard;
	private $settings;

	public function __construct( Dashboard $dashboard, Settings $settings ) {
		$this->dashboard = $dashboard;
		$this->settings = $settings;

		add_action( 'admin_menu', [ $this, 'register_w4_protocol_menu' ] );

		if ( ! empty( get_option( 'blackbox_bedrock_disabled' ) ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_menu_assets' ] );
		add_action( 'admin_head', [ $this, 'prevent_menu_cls' ], 0 );
		add_action( 'admin_menu', [ $this, 'group_wpmudev_plugins' ], 9999 );
		add_action( 'admin_menu', [ $this, 'register_blackbox_menu' ] );
		add_action( 'admin_footer', [ $this, 'output_accordion_js' ], 9999 );
	}

	public static function get_grouped_wpmudev_names() {
		return self::$grouped_wpmudev_names;
	}

	public static function get_grouped_wpmudev_icons() {
		return self::$grouped_wpmudev_icons;
	}

	public function enqueue_menu_assets() {
		if ( empty( get_option( 'blackbox_bedrock_wp_admin_menu_2030', '1' ) ) ) return;

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$assets_dir = dirname( __DIR__ ) . '/assets';

		wp_enqueue_style(
			'blackbox-menu-accordion',
			$assets_url . '/css/menu-accordion.css',
			[],
			filemtime( $assets_dir . '/css/menu-accordion.css' )
		);
	}

	public function prevent_menu_cls() {
		if ( empty( get_option( 'blackbox_bedrock_wp_admin_menu_2030', '1' ) ) ) return;
		echo '<style id="blackbox-menu-cls-prevention">#adminmenu{opacity:0;transition:opacity 0.25s ease-in-out;}body.blackbox-menu-grouped #adminmenu{opacity:1;}</style>';
	}

	public function register_w4_protocol_menu() {
		$icon_url = plugins_url( 'assets/images/webwork.png', dirname( __DIR__ ) . '/BlackBOX.php' );
		
		add_menu_page(
			'w⁴ Protocol',
			'w⁴ Protocol',
			'manage_options',
			'w4-protocol',
			[ $this->settings, 'settings_page_display' ],
			$icon_url,
			-2 // Position -2 places it at the very top
		);

		add_submenu_page(
			'w4-protocol',
			'Platform Upgrades',
			'Platform Upgrades',
			'manage_options',
			'w4-protocol',
			[ $this->settings, 'settings_page_display' ]
		);
	}

	public function register_blackbox_menu() {
		$icon_url = plugins_url( 'assets/images/obsidian.png', dirname( __DIR__ ) . '/BlackBOX.php' );
		
		add_menu_page(
			'BlackBOX',
			'BlackBOX',
			'manage_options',
			'blackbox-plugins',
			[ $this->dashboard, 'render_blackbox_page' ],
			$icon_url,
			-1 // Position -1 places it at the absolute top
		);

		// We must explicitly register a submenu with the identical slug.
		// Otherwise, WordPress will auto-hijack the parent menu link to point to the first WPMUDEV plugin.
		add_submenu_page(
			'blackbox-plugins',
			'Operations Matrix',
			'Operations Matrix',
			'manage_options',
			'blackbox-plugins',
			[ $this->dashboard, 'render_blackbox_page' ]
		);
	}

	public function group_wpmudev_plugins() {
		global $menu;

		$wpmudev_slugs = [
			'wpmudev',
			'forminator',
			'wp-defender',
			'wphb',
			'smush',
			'smush-pro',
			'hustle',
			'beehive',
			'snapshot',
			'branding',
			'wds_wizard',
			'shipper',
			'wpmudev-videos',
			'blc_dash' // Broken Link Checker
		];

		if ( ! empty( $menu ) ) {
			foreach ( $menu as $key => $item ) {
				$slug = $item[2];
				
				if ( in_array( $slug, $wpmudev_slugs, true ) ) {
					$title = wp_strip_all_tags( $item[0] );
					self::$grouped_wpmudev_names[ $slug ] = $title;
					
					if ( isset( $item[6] ) && ! empty( $item[6] ) ) {
						if ( strpos( $item[6], 'http' ) === 0 || strpos( $item[6], 'data:image' ) === 0 || strpos( $item[6], '/' ) === 0 ) {
							self::$grouped_wpmudev_icons[ $slug ] = $item[6];
						}
					}
				}
			}
		}
	}

	public function build_compass_group_map() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		$map = [];

		$route_remap = [
			'quests'        => 'questbook',
			'alphabet-soup' => 'newsroom',
			'freshmints'    => 'fresh-mints',
		];

		$group_to_panel = [
			'CMS'        => 'cms',
			'CRM'        => 'crm',
			'MA'         => 'ma',
			'POS'        => 'commerce',
			'BI'         => 'bi',
			'LXP'        => 'gamification',
			'ITSM'       => 'itsm',
			'OS'         => 'os',
			'Governance' => 'os',
			'Economics'  => 'commerce',
			'Community'  => '3rd',
			'Ecosystem'  => '3rd',
		];

		$default_groups = [
			'alphabet-soup'    => 'CMS',
			'magic-wand'       => 'CMS',
			'enchiridion'      => 'CMS',
			'quests'           => 'CRM',
			'magic-formula'    => 'CRM',
			'bomb-bag'         => 'MA',
			'enchanted-mirror' => 'MA',
			'gale-boomerang'   => 'MA',
			'golden-keys'      => 'MA',
			'lead-magnet'      => 'MA',
			'pegasus-boots'    => 'MA',
			'pixie-dust'       => 'MA',
			'silver-arrow'     => 'MA',
			'bazaar'           => 'POS',
			'treasure-trove'   => 'POS',
			'produce'          => 'POS',
			'moving-castle'    => 'BI',
			'treasure-map'     => 'BI',
			'xp'               => 'LXP',
			'bugnet'           => 'ITSM',
			'hookshot'         => 'ITSM',
			'lit-lamp'         => 'ITSM',
			'magic-cloak'      => 'ITSM',
			'midnight-nerd'    => 'ITSM',
			'mirror-shield'    => 'ITSM',
			'phantom-zone'     => 'ITSM',
			'thors-hammer'     => 'ITSM',
			'titans-mitt'      => 'ITSM',
			'event-horizon'    => 'OS',
			'phone'            => 'OS',
			'polos'            => 'OS',
			'bulletin-board'   => 'Community',
			'diego-lawfirm'    => 'Ecosystem',
			'fresh-mints'      => 'Ecosystem',
			'freshmints'       => 'Ecosystem',
			'glowitheflow'     => 'Ecosystem',
			'yellow-links'     => 'Ecosystem',
		];

		foreach ( $plugins as $path => $data ) {
			$text_domain = $data['TextDomain'] ?? '';
			$is_compass_plugin = strpos( $text_domain, 'xophz-compass-' ) === 0;
			if ( ! $is_compass_plugin ) continue;

			$slug = str_replace( 'xophz-compass-', '', $text_domain );

			$group = ! empty( $data['Group'] ) ? trim( $data['Group'] ) : '';

			if ( empty( $group ) ) {
				$plugin_file = WP_PLUGIN_DIR . '/' . $path;
				if ( file_exists( $plugin_file ) ) {
					$headers = get_file_data( $plugin_file, [ 'Group' => 'Group' ] );
					$group = ! empty( $headers['Group'] ) ? trim( $headers['Group'] ) : '';
				}
			}

			if ( empty( $group ) ) {
				$group = $default_groups[ $slug ] ?? '';
			}

			if ( empty( $group ) ) continue;

			$panel = $group_to_panel[ $group ] ?? ( $group_to_panel[ strtoupper( $group ) ] ?? '3rd' );
			$route_slug = $route_remap[ $slug ] ?? $slug;
			
			$folder = dirname( $path );
			$icon_file = WP_PLUGIN_DIR . '/' . $folder . '/icon.svg';
			if ( $slug === 'magic-formula' ) {
				$icon = plugins_url( 'xophz-compass/assets/magic-formula.svg' );
			} elseif ( file_exists( $icon_file ) ) {
				$icon = plugins_url( $folder . '/icon.svg' );
			} else {
				$icon = plugins_url( $text_domain . '/icon.svg' );
			}

			$map[ $route_slug ] = [
				'panel' => $panel,
				'icon'  => $icon,
			];
		}

		return $map;
	}

	public function output_accordion_js() {
		if ( empty( get_option( 'blackbox_bedrock_wp_admin_menu_2030', '1' ) ) ) return;

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$assets_dir = dirname( __DIR__ ) . '/assets';

		wp_enqueue_script(
			'blackbox-menu-accordion',
			$assets_url . '/js/menu-accordion.js',
			[],
			filemtime( $assets_dir . '/js/menu-accordion.js' ),
			true
		);

		wp_localize_script( 'blackbox-menu-accordion', 'blackbox_menu_config', [
			'bbIconUrl'       => plugins_url( 'assets/images/obsidian.png', dirname( __DIR__ ) . '/BlackBOX.php' ),
			'compassGroupMap' => $this->build_compass_group_map(),
			'wpmudevIcons'    => self::$grouped_wpmudev_icons,
		] );
	}
}

