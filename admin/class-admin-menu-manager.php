<?php
namespace BlackBOX\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Menu_Manager {

	private static $grouped_wpmudev_icons = [];
	private static $grouped_wpmudev_names = [];
	private static $settings_map = [
		// Classic WordPress Settings Pages
		'xophz-compass/xophz-compass.php'                               => 'options-general.php?page=w4-my-compass',
		'xophz-compass-diego-lawfirm/xophz-compass-diego-lawfirm.php'   => 'options-general.php?page=xophz-compass-diego-lawfirm',
		'xophz-compass-event-horizon/xophz-compass-event-horizon.php'   => 'options-general.php?page=w4-youmeos',
		'xophz-compass-fresh-mints/xophz-compass-fresh-mints.php'       => 'options-general.php?page=xophz-compass-freshmints',
		'xophz-compass-phone/xophz-compass-phone.php'                   => 'options-general.php?page=xophz-compass-phone',
		'xophz-compass-yellow-links/xophz-compass-yellow-links.php'     => 'options-general.php?page=xophz-compass-yellow-links',
		'xophz-kitchen-synk/xophz-kitchen-synk.php'                     => 'options-general.php?page=xophz-kitchen-synk',
		'xophz-thoth-reader-wp/xophz-thoth-reader.php'                 => 'options-general.php?page=xophz-thoth-reader',
		'xophz-compass-card-vault/xophz-compass-card-vault.php'         => 'options-general.php?page=xophz-compass-card-vault',
		'xophz-compass-bugnet/xophz-compass-bugnet.php'                 => 'edit.php?post_type=compass_bug&page=bugnet-github-settings',

		// COMPASS SPA Settings Routes
		'xophz-compass-bomb-bag/xophz-compass-bomb-bag.php'             => 'admin.php?page=xophz-compass#/bomb-bag/settings',
		'xophz-compass-bulletin-board/xophz-compass-bulletin-board.php' => 'admin.php?page=xophz-compass#/bulletin-board/settings',
		'xophz-compass-glowitheflow/xophz-compass-glowitheflow.php'     => 'admin.php?page=xophz-compass#/glowitheflow/settings',
		'xophz-compass-lead-magnet/xophz-compass-lead-magnet.php'       => 'admin.php?page=xophz-compass#/lead-magnet/settings',
		'xophz-compass-magic-formula/xophz-compass-magic-formula.php'   => 'admin.php?page=xophz-compass#/magic-formula/settings',
		'xophz-compass-pegasus-boots/xophz-compass-pegasus-boots.php'   => 'admin.php?page=xophz-compass#/pegasus-boots/settings',
		'xophz-compass-phantom-zone/xophz-compass-phantom-zone.php'     => 'admin.php?page=xophz-compass#/phantom-zone/settings',
		'xophz-compass-quests/xophz-compass-quests.php'                 => 'admin.php?page=xophz-compass#/questbook/settings',
		'xophz-compass-xp/xophz-compass-xp.php'                         => 'admin.php?page=xophz-compass#/xp/settings',
	];
	private $dashboard;
	private $settings;

	public function __construct( Dashboard $dashboard, Settings $settings ) {
		$this->dashboard = $dashboard;
		$this->settings = $settings;

		add_action( 'admin_menu', [ $this, 'register_w4_protocol_menu' ] );
		add_action( 'admin_menu', [ $this, 'organize_settings_submenu' ], 99999 );

		foreach ( array_keys( self::$settings_map ) as $plugin_file ) {
			add_filter( "plugin_action_links_{$plugin_file}", [ $this, 'add_ecosystem_plugin_action_links' ], 9999, 4 );
		}

		if ( ! empty( get_option( 'blackbox_bedrock_disabled' ) ) || ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ) {
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
		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$icon_url   = $assets_url . '/images/webwork.png';
		
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
		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$icon_url   = $assets_url . '/images/obsidian.png';
		
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
			'my-lawfirm'       => 'Ecosystem',
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
			'bbIconUrl'       => $assets_url . '/images/obsidian.png',
			'compassGroupMap' => $this->build_compass_group_map(),
			'wpmudevIcons'    => self::$grouped_wpmudev_icons,
		] );
	}

	/**
	 * Organize the Settings (options-general.php) submenu.
	 * Keeps standard WordPress Core settings at the top in canonical order,
	 * adds a visual separator class, and sorts all ecosystem/plugin settings A-Z.
	 */
	public function organize_settings_submenu() {
		global $submenu;

		if ( empty( $submenu['options-general.php'] ) || ! is_array( $submenu['options-general.php'] ) ) {
			return;
		}

		$core_slugs = [
			'options-general.php',
			'options-connectors.php',
			'options-writing.php',
			'options-reading.php',
			'options-discussion.php',
			'options-media.php',
			'options-permalink.php',
			'options-privacy.php',
		];

		$core_items   = [];
		$plugin_items = [];

		foreach ( $submenu['options-general.php'] as $item ) {
			$slug = $item[2] ?? '';
			if ( in_array( $slug, $core_slugs, true ) ) {
				$core_items[] = $item;
			} else {
				$plugin_items[] = $item;
			}
		}

		// Sort ecosystem/plugin settings alphabetically (A-Z) by menu title
		usort( $plugin_items, function( $a, $b ) {
			$title_a = wp_strip_all_tags( $a[0] ?? '' );
			$title_b = wp_strip_all_tags( $b[0] ?? '' );
			return strcasecmp( $title_a, $title_b );
		} );

		// Add separator class to the first plugin item if plugins exist
		if ( ! empty( $plugin_items ) ) {
			$first_class = isset( $plugin_items[0][4] ) ? trim( $plugin_items[0][4] ) : '';
			$plugin_items[0][4] = trim( $first_class . ' bb-settings-separator' );
		}

		$submenu['options-general.php'] = array_merge( $core_items, $plugin_items );
	}

	/**
	 * Centralized action link injector and deduplicator for ecosystem plugins on plugins.php.
	 * Runs at high priority (9999) on plugin_action_links_{$plugin_file} after plugin-level hooks.
	 * Injects a Settings link if missing, or deduplicates if multiple are present.
	 */
	public function add_ecosystem_plugin_action_links( $actions, $plugin_file, $plugin_data = [], $context = '' ) {
		$settings_seen = false;
		$cleaned_actions = [];

		foreach ( $actions as $key => $action ) {
			if ( stripos( $action, '>Settings<' ) !== false ) {
				if ( ! $settings_seen ) {
					$cleaned_actions['settings'] = $action;
					$settings_seen = true;
				}
				// Skip any duplicate settings links
				continue;
			}
			$cleaned_actions[ $key ] = $action;
		}

		if ( ! $settings_seen && isset( self::$settings_map[ $plugin_file ] ) ) {
			$url           = admin_url( self::$settings_map[ $plugin_file ] );
			$settings_link = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'blackbox-bedrock' ) . '</a>';
			$final_actions = [ 'settings' => $settings_link ];
			foreach ( $cleaned_actions as $key => $val ) {
				$final_actions[ $key ] = $val;
			}
			return $final_actions;
		}

		return $cleaned_actions;
	}
}

