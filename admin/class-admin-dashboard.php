<?php
namespace BlackBOX\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Dashboard {

	private static $valuations = [
		'beehive-analytics'              => [1188, 'Mixpanel Pro', 'Equivalent value based on enterprise analytics platforms offering custom event tracking and reporting.', 'gold', 99, 'portal'],
		'broken-link-checker'            => [1188, 'Ahrefs Lite', 'Comparable to entry-level SEO auditing tools for automated link monitoring.', 'bronze', 49, 'infrastructure'],
		'forminator'                     => [1188, 'Typeform Pro', 'Equivalent to premium form builders with conditional logic, payments, and API integrations.', 'silver', 99, 'spark'],
		'hustle'                         => [588,  'OptinMonster', 'Valued against leading lead generation and popup management software.', 'silver', 69, 'portal'],
		'shipper'                        => [348,  'BlogVault Migrate', 'Comparable to professional site migration and staging services.', 'gold', 49, 'infrastructure'],
		'snapshot-backups'               => [300,  'VaultPress', 'Value based on managed daily cloud backup and restore solutions.', 'bronze', 49, 'infrastructure'],
		'ultimate-branding'              => [228,  'White Label CMS', 'Equivalent to premium white-labeling add-ons for agencies.', 'platinum', 49, 'portal'],
		'wp-defender'                    => [2388, 'Sucuri Platform', 'Comparable to enterprise-grade web application firewalls and malware scanners.', 'uranium', 149, 'portal'],
		'wp-hummingbird'                 => [588,  'WP Rocket + CDN', 'Valued against top-tier caching plugins bundled with premium CDN services.', 'silver', 69, 'spark'],
		'wp-smush-pro'                   => [1068, 'Cloudinary Pro', 'Equivalent to professional image optimization and delivery networks.', 'silver', 79, 'spark'],
		'wpmu-dev-seo'                   => [1548, 'SEMrush Pro', 'Comparable to comprehensive on-page SEO analysis and auditing suites.', 'gold', 99, 'portal'],
		'wpmudev-updates'                => [300,  'ManageWP Business', 'Value based on centralized multi-site management dashboards.', 'quantum', 29, 'infrastructure'],
		'wpmudev-videos'                 => [360,  'LinkedIn Learning', 'Equivalent to premium video training library subscriptions.', 'quantum', 29, 'infrastructure'],
		'xophz-compass'                  => [3600, 'Salesforce Platform', 'The core framework provides foundational CRM and application architecture comparable to enterprise PaaS.', 'quantum', 199, 'portal'],
		'xophz-compass-alphabet-soup'    => [2388, 'Contentful CMS', 'A headless-ready content management engine valued against mid-market CMS platforms.', 'quantum', 79, 'spark'],
		'xophz-compass-bazaar'           => [2388, 'Shopify Pro', 'Advanced eCommerce foresight and shop management equivalent to premium store platforms.', 'silver', 149, 'portal'],
		'xophz-compass-bomb-bag'         => [1188, 'Mailchimp Pro', 'A full-featured email marketing and automated drip sequence engine.', 'bronze', 99, 'portal'],
		'xophz-compass-bugnet'           => [1500, 'Jira Service Mgmt', 'A comprehensive issue tracking and beta management system.', 'quantum', 99, 'portal'],
		'xophz-compass-enchanted-mirror' => [948,  'Hotjar Plus', 'Audience comparison and behavioral analytics equivalent to premium insight tools.', 'gold', 89, 'portal'],
		'xophz-compass-enchiridion'      => [1200, 'Confluence Team', 'Knowledge base and documentation engine comparable to enterprise wikis.', 'quantum', 79, 'spark'],
		'xophz-compass-event-horizon'    => [6000, 'Custom Portal SaaS', 'A complete WebGL desktop environment and application portal.', 'quantum', 199, 'portal'],
		'xophz-compass-gale-boomerang'   => [2100, 'Pendo / Mixpanel', 'Advanced traffic, server load, and visitor resonance analytics engine.', 'gold', 129, 'portal'],
		'xophz-compass-golden-keys'      => [2760, 'Auth0 Pro', 'Identity and access management engine with secure keyword-based routing.', 'gold', 149, 'portal'],
		'xophz-compass-lead-magnet'      => [960,  'HubSpot Starter', 'Lead generation and inbound marketing capture engine.', 'silver', 79, 'portal'],
		'xophz-compass-lit-lamp'         => [588,  'StatusPage Pro', 'System health monitoring and transparent status reporting.', 'silver', 49, 'spark'],
		'xophz-compass-magic-cloak'      => [600,  'MemberPress Pro', 'Content restriction, membership tiers, and access control.', 'silver', 79, 'portal'],
		'xophz-compass-magic-formula'    => [708,  'Elementor Pro', 'Advanced visual shortcode generation and AI form conjuring.', 'silver', 79, 'spark'],
		'xophz-compass-magic-wand'       => [480,  'Yoast SEO Premium', 'Automated SEO enhancement and metadata optimization engine.', 'silver', 69, 'portal'],
		'xophz-compass-midnight-nerd'    => [1800, 'Zendesk Suite', 'ITSM support ticket and helpdesk management system.', 'silver', 99, 'portal'],
		'xophz-compass-mirror-shield'    => [468,  'Cloudflare WAF', 'Security engine reflecting attacks and protecting site access.', 'silver', 69, 'portal'],
		'xophz-compass-moving-castle'    => [468,  'WP Migrate Pro', 'Cross-site data migration and database syncing architecture.', 'gold', 69, 'portal'],
		'xophz-compass-pegasus-boots'    => [840,  'NitroPack Pro', 'Extreme performance optimization and speed enhancement engine.', 'gold', 79, 'portal'],
		'xophz-compass-phantom-zone'     => [600,  'WP Stagecoach', 'Error page management and staging environment isolation.', 'silver', 49, 'portal'],
		'xophz-compass-pixie-dust'       => [588,  'Mailtrack Pro', 'Invisible email open tracking and correspondence telemetry.', 'silver', 49, 'portal'],
		'xophz-compass-quests'           => [5400, 'HubSpot CRM Pro', 'A fully-featured Customer Relationship Management system with comm-links.', 'bronze', 149, 'portal'],
		'xophz-compass-silver-arrow'     => [948,  'VWO / Optimizely', 'A/B split testing engine for optimizing conversion rates.', 'gold', 99, 'portal'],
		'xophz-compass-thors-hammer'     => [468,  'WP-CLI Pro Tools', 'Advanced developer tools and systemic command execution.', 'silver', 49, 'spark'],
		'xophz-compass-titans-mitt'      => [468,  'WP All Import Pro', 'Heavy-duty data import and object management engine.', 'uranium', 69, 'portal'],
		'xophz-compass-treasure-map'     => [1188, 'Databox Pro', 'Executive dashboarding and KPI visualization engine.', 'uranium', 99, 'portal'],
		'xophz-compass-treasure-trove'   => [1188, 'WooCommerce Ext.', 'Advanced eCommerce enhancements and payment gateways.', 'silver', 149, 'portal'],
		'xophz-compass-xp'               => [708,  'GamiPress Pro', 'Comprehensive gamification, achievements, and user reward system.', 'quantum', 79, 'portal'],
	];

	public static function get_tesseract_tiers() {
		return [
			'all' => [
				'id'    => 'all',
				'name'  => 'All Engines',
				'icon'  => 'fad fa-boxes',
				'color' => '#62c9ff',
				'track' => 'all',
				'price' => '',
			],
			'quantum' => [
				'id'    => 'quantum',
				'name'  => 'Quantum',
				'icon'  => 'fal fa-square',
				'color' => '#62c9ff',
				'track' => 'micro',
				'price' => '$14.99/mo',
			],
			'bronze' => [
				'id'    => 'bronze',
				'name'  => 'Bronze',
				'icon'  => 'fal fa-box',
				'color' => '#cd7f32',
				'track' => 'micro',
				'price' => '$34.99/mo',
			],
			'silver' => [
				'id'    => 'silver',
				'name'  => 'Silver',
				'icon'  => 'fal fa-box-full',
				'color' => '#c0c0c0',
				'track' => 'micro',
				'price' => '$74.99/mo',
			],
			'gold' => [
				'id'    => 'gold',
				'name'  => 'Gold',
				'icon'  => 'fal fa-box-check',
				'color' => '#d9be6f',
				'track' => 'macro',
				'price' => '$129.99/mo',
			],
			'platinum' => [
				'id'    => 'platinum',
				'name'  => 'Platinum',
				'icon'  => 'fal fa-gem',
				'color' => '#a0b2c6',
				'track' => 'macro',
				'price' => '$299/mo',
			],
			'uranium' => [
				'id'    => 'uranium',
				'name'  => 'Uranium',
				'icon'  => 'fal fa-atom-alt',
				'color' => '#3dee98',
				'track' => 'omni',
				'price' => '$650/mo',
			],
			'titanium' => [
				'id'    => 'titanium',
				'name'  => 'Titanium',
				'icon'  => 'fal fa-shield-virus',
				'color' => '#3dee98',
				'track' => 'omni',
				'price' => '$1,250/mo',
			],
			'palladium' => [
				'id'    => 'palladium',
				'name'  => 'Palladium',
				'icon'  => 'fal fa-crown',
				'color' => '#e6e6fa',
				'track' => 'omni',
				'price' => '$2,499/mo',
			],
		];
	}

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'wp_ajax_blackbox_toggle_plugin', [ $this, 'ajax_toggle_plugin' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_dashboard_assets' ] );
	}

	public function enqueue_dashboard_assets( $hook ) {
		$is_dashboard = ( isset( $_GET['page'] ) && $_GET['page'] === 'blackbox-plugins' ) || $hook === 'toplevel_page_blackbox-plugins';
		if ( ! $is_dashboard ) {
			return;
		}

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$assets_dir = dirname( __DIR__ ) . '/assets';

		wp_enqueue_style(
			'blackbox-fontawesome',
			$assets_url . '/vendor/fontawesome/css/all.min.css',
			[],
			'5.15.4'
		);

		wp_enqueue_style(
			'blackbox-dashboard',
			$assets_url . '/css/dashboard.css',
			[ 'blackbox-fontawesome' ],
			filemtime( $assets_dir . '/css/dashboard.css' )
		);

		wp_enqueue_script(
			'blackbox-dashboard',
			$assets_url . '/js/dashboard.js',
			[],
			filemtime( $assets_dir . '/js/dashboard.js' ),
			true
		);

		wp_add_inline_script(
			'blackbox-dashboard',
			'window.blackbox_api = ' . wp_json_encode( [
				'root'  => esc_url_raw( rest_url( 'blackbox/v1/' ) ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
			] ) . '; window.blackbox_toggle_nonce = ' . wp_json_encode( wp_create_nonce( 'blackbox_toggle' ) ) . ';',
			'before'
		);
	}

	public static function get_valuations() {
		return self::$valuations;
	}

	public function render_blackbox_page() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$all_plugins = get_plugins();
		$active_plugins = (array) get_option( 'active_plugins', [] );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', [] ) ) );
		}

		$wpmudev_slugs = [
			'beehive-analytics',
			'broken-link-checker',
			'forminator',
			'hustle',
			'shipper',
			'snapshot-backups',
			'ultimate-branding',
			'wp-defender',
			'wp-hummingbird',
			'wp-smush-pro',
			'wpmu-dev-seo',
			'wpmudev-updates',
			'wpmudev-videos'
		];

		$display_plugins = [];

		$folder_to_slug = [
			'beehive-analytics' => 'beehive',
			'broken-link-checker' => 'blc_dash',
			'forminator' => 'forminator',
			'hustle' => 'hustle',
			'shipper' => 'shipper',
			'snapshot-backups' => 'snapshot',
			'ultimate-branding' => 'branding',
			'wp-defender' => 'wp-defender',
			'wp-hummingbird' => 'wphb',
			'wp-smush-pro' => 'smush',
			'wpmu-dev-seo' => 'wds_wizard',
			'wpmudev-updates' => 'wpmudev',
			'wpmudev-videos' => 'wpmudev-videos'
		];

		$wpmudev_branding_icons = [
			'broken-link-checker' => 'https://ps.w.org/broken-link-checker/assets/icon-256x256.png',
			'forminator'          => 'https://ps.w.org/forminator/assets/icon-256x256.gif',
			'hustle'              => 'https://ps.w.org/wordpress-popup/assets/icon-256x256.gif',
			'ultimate-branding'   => 'https://ps.w.org/branda-white-labeling/assets/icon-256x256.gif',
			'wp-defender'         => 'https://ps.w.org/defender-security/assets/icon-256x256.gif',
			'wp-hummingbird'      => 'https://ps.w.org/hummingbird-performance/assets/icon-256x256.gif',
			'wp-smush-pro'        => 'https://ps.w.org/wp-smushit/assets/icon-256x256.gif',
			'wpmu-dev-seo'        => 'https://ps.w.org/smartcrawl-seo/assets/icon-256x256.gif',
		];

		$wpmudev_default_icons = [
			'beehive-analytics'   => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.5 9.09095H12.7V13.6364H14.5V9.09095Z" fill="#62c9ff"/><path d="M9.99988 2.07275L17.1999 6.07272V13.9273L9.99988 17.9272L2.79988 13.9273V6.07272L9.99988 2.07275ZM9.99988 0L0.999878 5V15L9.99988 20L18.9999 15V5L9.99988 0Z" fill="#62c9ff"/><path d="M7.29991 11.8182H5.49991V13.6364H7.29991V11.8182Z" fill="#62c9ff"/><path d="M10.8999 6.36368H9.09995V13.6364H10.8999V6.36368Z" fill="#62c9ff"/></svg>' ),
			'broken-link-checker' => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.58579 11.4142L11.4142 8.58579M13.5355 6.46447C14.7071 5.29289 16.6066 5.29289 17.7782 6.46447C18.9497 7.63604 18.9497 9.53553 17.7782 10.7071L14.9497 13.5355C13.7782 14.7071 11.8787 14.7071 10.7071 13.5355M9.29289 6.46447C8.12132 5.29289 6.22183 5.29289 5.05025 6.46447L2.22183 9.29289C1.05025 10.4645 1.05025 12.364 2.22183 13.5355C3.3934 14.7071 5.29289 14.7071 6.46447 13.5355" stroke="#62c9ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>' ),
			'forminator'          => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.5067 1.79874H16.2222C16.6937 1.79874 17.1459 1.99053 17.4793 2.33187C17.8127 2.67321 18 3.13614 18 3.61887V18.1799C18 18.6626 17.8127 19.1255 17.4793 19.4669C17.1459 19.8082 16.6937 20 16.2222 20H3.77778C3.30628 20 2.85412 19.8082 2.52072 19.4669C2.18733 19.1255 2 18.6626 2 18.1799V3.61887C2 3.13614 2.18733 2.67321 2.52072 2.33187C2.85412 1.99053 3.30628 1.79874 3.77778 1.79874H7.49333C7.68017 1.27168 8.02098 0.816284 8.46946 0.494469C8.91793 0.172654 9.45234 0 10 0C10.5477 0 11.0821 0.172654 11.5305 0.494469C11.979 0.816284 12.3198 1.27168 12.5067 1.79874ZM10.4938 1.9521C10.3476 1.8521 10.1758 1.79874 10 1.79874C9.76425 1.79874 9.53817 1.89464 9.37147 2.0653C9.20477 2.23597 9.11111 2.46744 9.11111 2.7088C9.11111 2.8888 9.16323 3.06472 9.2609 3.21438C9.35858 3.36404 9.49741 3.48072 9.65983 3.5496C9.82225 3.61848 10.001 3.63648 10.1734 3.60137C10.3458 3.56625 10.5042 3.47958 10.6285 3.3523C10.7528 3.22503 10.8375 3.06286 10.8718 2.88633C10.9061 2.70979 10.8885 2.52682 10.8212 2.36053C10.754 2.19424 10.64 2.0521 10.4938 1.9521ZM3.77778 3.61887V18.1799H16.2222V3.61887H13.5556V5.43899H6.44444V3.61887H3.77778ZM6.44442 10.8987H13.5555C13.7913 10.8987 14.0174 10.9946 14.1841 11.1653C14.3508 11.3359 14.4444 11.5674 14.4444 11.8087C14.4444 12.0501 14.3508 12.2816 14.1841 12.4522C14.0174 12.6229 13.7913 12.7188 13.5555 12.7188H6.44442C6.20867 12.7188 5.98259 12.6229 5.8159 12.4522C5.6492 12.2816 5.55553 12.0501 5.55553 11.8087C5.55553 11.5674 5.6492 11.3359 5.8159 11.1653C5.98259 10.9946 6.20867 10.8987 6.44442 10.8987ZM13.5555 8.16849H6.44442C6.20867 8.16849 5.98259 8.26438 5.8159 8.43505C5.6492 8.60572 5.55553 8.83719 5.55553 9.07855C5.55553 9.31992 5.6492 9.55138 5.8159 9.72205C5.98259 9.89272 6.20867 9.98862 6.44442 9.98862H13.5555C13.7913 9.98862 14.0174 9.89272 14.1841 9.72205C14.3508 9.55138 14.4444 9.31992 14.4444 9.07855C14.4444 8.83719 14.3508 8.60572 14.1841 8.43505C14.0174 8.26438 13.7913 8.16849 13.5555 8.16849ZM10 13.6289H13.5556C13.7913 13.6289 14.0174 13.7248 14.1841 13.8954C14.3508 14.0661 14.4444 14.2976 14.4444 14.5389C14.4444 14.7803 14.3508 15.0118 14.1841 15.1824C14.0174 15.3531 13.7913 15.449 13.5556 15.449H10C9.76425 15.449 9.53817 15.3531 9.37148 15.1824C9.20478 15.0118 9.11111 14.7803 9.11111 14.5389C9.11111 14.2976 9.20478 14.0661 9.37148 13.8954C9.53817 13.7248 9.76425 13.6289 10 13.6289Z" fill="#62c9ff"/></svg>' ),
			'hustle'              => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwXzE3XzkpIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xNy41IDIwQzE2LjgzNyAyMCAxNi4yMDExIDE5LjczNjYgMTUuNzMyMiAxOS4yNjc4QzE1LjI2MzQgMTguNzk4OSAxNSAxOC4xNjMgMTUgMTcuNUMxNSAxNS41NjM4IDEyLjM0NDIgMTUuMTE3IDguOTk5NjkgMTUuMDIzMVYxNi45OTk5QzkuMDEyMTggMTcuMzk3MiA4Ljk0MzE1IDE3Ljc5MjkgOC43OTY4MSAxOC4xNjI1QzguNjUwNDYgMTguNTMyMiA4LjQyOTkgMTguODY3OSA4LjE0ODggMTkuMTQ5QzcuODY3NjkgMTkuNDMwMSA3LjUzMTk3IDE5LjY1MDYgNy4xNjIzNSAxOS43OTdDNi43OTI3MiAxOS45NDMzIDYuMzk3MDMgMjAuMDEyNCA1Ljk5OTY5IDE5Ljk5OTlDNS42MDIzNCAyMC4wMTI0IDUuMjA2NjUgMTkuOTQzMyA0LjgzNzAzIDE5Ljc5N0M0LjQ2NzQgMTkuNjUwNiA0LjEzMTY4IDE5LjQzMDEgMy44NTA1OCAxOS4xNDlDMy41Njk0NyAxOC44Njc5IDMuMzQ4OTEgMTguNTMyMiAzLjIwMjU3IDE4LjE2MjVDMy4wNTYyMiAxNy43OTI5IDIuOTg3MiAxNy4zOTcyIDIuOTk5NjkgMTYuOTk5OVYxNC43ODQ2QzIuNTk4OTcgMTQuNjI3MSAyLjIzMTI4IDE0LjM4NzkgMS45MjE2OSAxNC4wNzgzQzEuMzY3MjYgMTMuNTIzOSAxLjAzODc0IDEyLjc4MzEgMC45OTk5OTcgMTJWMTEuNDE0MkMwLjc5MTA0MyAxMS4zNDAzIDAuNTk5MDM0IDExLjIyMDQgMC40MzkzMzEgMTEuMDYwN0MwLjE1ODAyNiAxMC43Nzk0IDAgMTAuMzk3OCAwIDEwQzAgOS42MDIxNyAwLjE1ODAyNiA5LjIyMDY0IDAuNDM5MzMxIDguOTM5MzNDMC41OTkwMzQgOC43Nzk2MyAwLjc5MTA0MyA4LjY1OTY2IDAuOTk5OTk3IDguNTg1NzhWOEMxLjAzODc0IDcuMjE2ODcgMS4zNjcyNiA2LjQ3NjEyIDEuOTIxNjkgNS45MjE2OUMyLjQ3NjEyIDUuMzY3MjYgMy4yMTY4NyA1LjAzODc0IDQgNUg3QzExLjM4IDUgMTUgNC44MSAxNSAyLjVDMTUgMS44MzY5NiAxNS4yNjM0IDEuMjAxMDggMTUuNzMyMiAwLjczMjIzOUMxNi4yMDExIDAuMjYzMzk4IDE2LjgzNyAwIDE3LjUgMEMxOC4xNjMgMCAxOC43OTg5IDAuMjYzMzk4IDE5LjI2NzggMC43MzIyMzlDMTkuNzM2NiAxLjIwMTA4IDIwIDEuODM2OTYgMjAgMi41VjE3LjVDMjAgMTguMTYzIDE5LjczNjYgMTguNzk4OSAxOS4yNjc4IDE5LjI2NzhDMTguNzk4OSAxOS43MzY2IDE4LjE2MyAyMCAxNy41IDIwWk00Ljk5OTY5IDE1SDYuOTk5NjlWMTYuOTk5OUM2Ljk5OTY5IDE3LjI2NTEgNi44OTQzMiAxNy41MTk0IDYuNzA2NzggMTcuNzA3QzYuNTE5MjQgMTcuODk0NSA2LjI2NDkgMTcuOTk5OSA1Ljk5OTY5IDE3Ljk5OTlDNS43MzQ0NyAxNy45OTk5IDUuNDgwMTMgMTcuODk0NSA1LjI5MjU5IDE3LjcwN0M1LjEwNTA2IDE3LjUxOTQgNC45OTk2OSAxNy4yNjUxIDQuOTk5NjkgMTYuOTk5OVYxNVpNNCA3QzMuNzQ3MDMgNy4wMzQ3MyAzLjUxMjM0IDcuMTUxMjYgMy4zMzE3OCA3LjMzMTgyQzMuMTUxMjMgNy41MTIzNyAzLjAzNDczIDcuNzQ3MDMgMyA4VjEyQzMuMDM0NzMgMTIuMjUzIDMuMTUxMjMgMTIuNDg3NiAzLjMzMTc4IDEyLjY2ODJDMy41MTIzNCAxMi44NDg3IDMuNzQ3MDMgMTIuOTY1MyA0IDEzSDdDMTEgMTMgMTcgMTMgMTcgMTcuNUMxNyAxNy42MzI2IDE3LjA1MjcgMTcuNzU5OCAxNy4xNDY0IDE3Ljg1MzVDMTcuMjQwMiAxNy45NDczIDE3LjM2NzQgMTggMTcuNSAxOEMxNy42MzI2IDE4IDE3Ljc1OTggMTcuOTQ3MyAxNy44NTM2IDE3Ljg1MzVDMTcuOTQ3MyAxNy43NTk4IDE4IDE3LjYzMjYgMTggMTcuNVYyLjVDMTggMi4zNjczOSAxNy45NDczIDIuMjQwMjIgMTcuODUzNiAyLjE0NjQ1QzE3Ljc1OTggMi4wNTI2OSAxNy42MzI2IDIgMTcuNSAyQzE3LjM2NzQgMiAxNy4yNDAyIDIuMDUyNjkgMTcuMTQ2NCAyLjE0NjQ1QzE3LjA1MjcgMi4yNDAyMiAxNyAyLjM2NzM5IDE3IDIuNUMxNyA3IDExIDcgNyA3SDRaTTguNTcwMTggMTJWMTAuMjlMMTIuMDAwMiA4VjkuNzFMMTYuMDAwMiA4TDEwLjg2MDIgMTJWMTAuMjlMOC41NzAxOCAxMloiIGZpbGw9IiM2MmM5ZmYiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMF8xN185Ij4KPHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBmaWxsPSJ3aGl0ZSIvPgo8L2NsaXBQYXRoPgo8L2RlZnM+Cjwvc3ZnPgo=',
			'shipper'             => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 14.5V17.5C19 18.3284 18.3284 19 17.5 19H2.5C1.67157 19 1 18.3284 1 17.5V14.5" stroke="#62c9ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 1V13M10 1L5 6M10 1L15 6" stroke="#62c9ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>' ),
			'snapshot-backups'    => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="16px" height="18px" viewBox="0 0 16 18" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="Wp-Menu" transform="translate(-11.000000, -397.000000)" fill="#62c9ff"><path d="M11.8958333,400.71599 L17.2291667,397.536993 L13.6666667,403.873508 L11.8958333,400.71599 Z M16.9166667,402.305489 L21.0833333,402.305489 L23.2083333,406.085919 L21.125,409.694511 L16.9166667,409.694511 L16.9166667,409.673031 L14.8541667,406 L16.9166667,402.305489 Z M25.2291667,400.178998 L19.8958333,397 L18.1041667,400.178998 L25.2291667,400.178998 Z M11,403.357995 L14.5625,409.694511 L11,409.694511 L11,403.357995 Z M23.4375,402.305489 L27,402.305489 L27,408.642005 L23.4375,402.305489 Z M26.1041667,411.28401 L20.8125,414.441527 L24.375,408.190931 L26.1041667,411.28401 Z M18.125,414.97852 L19.9166667,411.821002 L12.7708333,411.821002 L18.1041667,415 L18.125,414.97852 Z" id="snapshot-icon"></path></g></g></svg>' ),
			'ultimate-branding'   => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.23 8.06L11.17 2H2V11.1L8.85999 17.2L17.23 8.06ZM0 0H12L20 8L9 20L0 12V0ZM3.66664 3.25281C3.91331 3.08799 4.20334 3 4.50001 3C4.89784 3 5.27938 3.15803 5.56068 3.43933C5.84199 3.72064 6.00001 4.10218 6.00001 4.5C6.00001 4.79667 5.91203 5.0867 5.7472 5.33337C5.58238 5.58005 5.34814 5.77227 5.07405 5.8858C4.79996 5.99933 4.49832 6.02907 4.20735 5.97119C3.91638 5.91331 3.64912 5.77045 3.43934 5.56067C3.22956 5.35089 3.0867 5.08364 3.02882 4.79266C2.97094 4.50169 3.00068 4.20005 3.11421 3.92596C3.22774 3.65188 3.41997 3.41763 3.66664 3.25281Z" fill="#62c9ff"/></svg>' ),
			'wp-defender'         => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.44 1.12125L2.94 2.43125C2.09 2.68125 1.5 3.46125 1.5 4.35125V8.66125C1.5 11.5312 4.72 14.9612 8 14.9612C11.28 14.9612 14.5 11.5312 14.5 8.66125V4.35125C14.5 3.46125 13.91 2.68125 13.06 2.43125L8.56 1.12125C8.2 1.01125 7.81 1.01125 7.44 1.12125ZM13.06 8.66125C13.06 10.6912 10.53 13.5612 8 13.5612V7.96125H2.94V3.90125L8 2.43125V7.96125H13.06V8.66125Z" fill="#62c9ff"/></svg>' ),
			'wp-hummingbird'      => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.7326 3.2L13.9481 4.05L13.2596 10.2295L10.9901 13.2555L10.7606 13.5615L10.6841 13.944L10.2422 16.137L8.27018 15.287L10.2761 12.7795L10.5481 12.4395L10.6246 12.006L11.4348 7.23704L11.4408 7.23749L11.4451 7.17626L12.1206 3.2H12.7326ZM10.7096 1.5L10.0555 5.42467L0 4.67049L9.10861 11.1063L9.00966 11.7L5.60972 15.95L11.5596 18.5L12.4096 14.25L14.9596 10.85L15.6459 4.67319L20 4.05093V3.20062H15.8095L15.8095 3.2L13.3191 1.5H13.2596H10.7096ZM6.0009 6.82949L9.41994 9.23827L9.77423 7.1125L6.0009 6.82949Z" fill="#62c9ff"/></svg>' ),
			'wp-smush-pro'        => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.00078 0.799805C4.04078 0.799805 0.800781 4.0398 0.800781 7.9998C0.800781 11.9598 4.04078 15.1998 8.00078 15.1998C11.9608 15.1998 15.2008 11.9598 15.2008 7.9998C15.2008 4.0398 11.9608 0.799805 8.00078 0.799805ZM8.00078 13.7598C6.84878 13.7598 5.84078 12.7518 5.84078 11.5998C5.84078 10.4478 6.84878 9.4398 8.00078 9.4398C9.15278 9.4398 10.1608 10.4478 10.1608 11.5998C10.1608 12.7518 9.15278 13.7598 8.00078 13.7598ZM11.8888 10.5918C11.8168 10.8798 11.7448 11.1678 11.6008 11.4558C11.5288 10.5198 11.1688 9.7278 10.5208 9.0798C9.87278 8.3598 8.93678 7.9998 8.00078 7.9998C7.28078 7.9998 6.56078 8.2158 5.98478 8.5758C5.40878 8.9358 4.90478 9.5118 4.68878 10.1598C4.54478 10.5918 4.40078 11.0238 4.40078 11.4558C4.18478 10.9518 4.04078 10.3758 4.04078 9.7998C4.04078 8.7198 4.47278 7.7118 5.19278 6.9918C5.91278 6.2718 6.92078 5.8398 8.00078 5.8398C8.79278 5.8398 9.58478 6.0558 10.2328 6.4878C10.8808 6.9198 11.3848 7.5678 11.6728 8.2878C11.9608 9.0078 12.0328 9.7998 11.8888 10.5918ZM13.3288 10.0158C13.3288 9.9438 13.3288 9.8718 13.3288 9.7998C13.3288 8.3598 12.7528 6.9918 11.7448 5.9838C10.7368 4.9758 9.44078 4.3998 8.00078 4.3998C6.92078 4.3998 5.91278 4.6878 4.97678 5.3358C4.11278 5.9118 3.39278 6.7758 2.96078 7.7838C2.67278 8.5038 2.52878 9.2958 2.52878 10.0878C2.38478 9.3678 2.24078 8.7198 2.24078 7.9998C2.24078 6.4878 2.81678 4.9758 3.89678 3.8958C4.97678 2.8158 6.48878 2.2398 8.00078 2.2398C9.15278 2.2398 10.2328 2.5998 11.1688 3.1758C12.1048 3.8238 12.8248 4.6878 13.2568 5.7678C13.7608 6.8478 13.9048 7.9998 13.6168 9.1518C13.5448 9.43981 13.4728 9.7278 13.3288 10.0158Z" fill="#62c9ff"/></svg>' ),
			'wpmu-dev-seo'        => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.4446 3.34827C13.129 2.46922 11.5823 2 10 2C7.87827 2 5.84343 2.84285 4.34314 4.34314C2.84285 5.84343 2 7.87827 2 10C2 11.0501 2.20668 12.0846 2.60267 13.0462L10.6489 5H8.99999V4H12H13V8H12V6.3359L3.57263 14.7633C4.1858 15.5907 4.9514 16.2898 5.82541 16.8244L10.6499 12H8.99997V11H12H13V15H12V13.3369L7.68051 17.6564C8.9348 18.0364 10.2675 18.1035 11.5607 17.8463C13.1126 17.5376 14.538 16.7757 15.6569 15.6569C16.7757 14.538 17.5376 13.1126 17.8463 11.5607C18.1549 10.0089 17.9966 8.40035 17.3911 6.93854C16.7856 5.47673 15.7602 4.22732 14.4446 3.34827ZM12 5.00006V5H11.9999L12 5.00006ZM4.44428 1.6853C6.08877 0.586489 8.02219 0 10 0C12.6522 0 15.1957 1.05359 17.071 2.92895C18.9464 4.80432 20 7.34783 20 10C20 11.9778 19.4135 13.9112 18.3147 15.5557C17.2159 17.2002 15.6541 18.4819 13.8268 19.2388C11.9996 19.9956 9.98888 20.1937 8.04908 19.8079C6.10927 19.422 4.32748 18.4696 2.92896 17.071C1.53043 15.6725 0.577995 13.8907 0.192143 11.9509C-0.193709 10.0111 0.00435805 8.00042 0.761235 6.17316C1.51811 4.3459 2.79979 2.78412 4.44428 1.6853Z" fill="#62c9ff"/></svg>' ),
			'wpmudev-updates'     => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.91282 3.91087C1.63883 4.37887 1.49602 4.91528 1.50009 5.4611L1.50009 17.4622L3.70066 17.4622L3.70066 5.45906C3.70041 5.36562 3.71895 5.27313 3.75514 5.18736C3.79133 5.1016 3.84439 5.02441 3.91099 4.96063C4.06577 4.82307 4.26374 4.74734 4.46857 4.74734C4.67341 4.74734 4.87138 4.82307 5.02616 4.96063C5.09221 5.02474 5.14477 5.10204 5.1806 5.18776C5.21643 5.27347 5.23477 5.36581 5.2345 5.45906L5.2345 14.496C5.22425 14.8878 5.29258 15.2776 5.43525 15.6411C5.57793 16.0047 5.79191 16.3344 6.06393 16.6098C6.46926 17.0329 6.98871 17.3222 7.55557 17.4403C8.12243 17.5585 8.71081 17.5003 9.24513 17.273C9.77945 17.0458 10.2353 16.66 10.5542 16.1652C10.873 15.6703 11.0403 15.0891 11.0346 14.496L11.0346 5.45906C11.033 5.36654 11.0498 5.27465 11.0839 5.18898C11.118 5.1033 11.1687 5.02561 11.233 4.96063C11.3071 4.88829 11.3946 4.83207 11.4905 4.79536C11.5863 4.75865 11.6884 4.7422 11.7906 4.74701C11.8928 4.74148 11.9951 4.75759 12.091 4.79434C12.187 4.83109 12.2745 4.88769 12.3482 4.96063C12.4148 5.02441 12.4678 5.1016 12.504 5.18736C12.5402 5.27313 12.5587 5.36562 12.5585 5.45906L12.5585 14.496C12.5483 14.8876 12.6165 15.2772 12.7588 15.6407C12.9011 16.0042 13.1146 16.334 13.3859 16.6098C13.6579 16.8898 13.9828 17.1099 14.3408 17.2565C14.6987 17.4031 15.0821 17.4731 15.4674 17.4622C16.2593 17.4718 17.0239 17.1662 17.6005 16.6098C17.8904 16.345 18.1209 16.0189 18.2761 15.654C18.4313 15.2891 18.5075 14.894 18.4994 14.496L18.4994 2.50303L16.2969 2.50303L16.2969 14.496C16.2984 14.5885 16.2816 14.6804 16.2475 14.7661C16.2134 14.8518 16.1627 14.9295 16.0984 14.9944C15.9437 15.132 15.7457 15.2077 15.5409 15.2077C15.336 15.2077 15.1381 15.132 14.9833 14.9944C14.917 14.9304 14.8641 14.8532 14.8279 14.7675C14.7918 14.6818 14.773 14.5894 14.7729 14.496L14.7729 5.45906C14.7829 5.06751 14.7146 4.67801 14.5723 4.3145C14.43 3.951 14.2167 3.62117 13.9455 3.34529C13.6739 3.06817 13.3502 2.85045 12.9942 2.70533C12.6381 2.56021 12.257 2.49069 11.8739 2.501C11.0807 2.48839 10.3134 2.79094 9.73287 3.34529C9.44323 3.61043 9.21281 3.93653 9.05734 4.30133C8.90187 4.66613 8.825 5.06103 8.83201 5.45906L8.83201 14.496C8.81366 14.6717 8.73258 14.8343 8.60437 14.9524C8.47617 15.0705 8.30988 15.1359 8.13751 15.1359C7.96514 15.1359 7.79885 15.0705 7.67065 14.9524C7.54244 14.8343 7.46136 14.6717 7.44301 14.496L7.44301 5.45906C7.44762 4.91289 7.30403 4.37616 7.0283 3.90883C6.76827 3.45456 6.38493 3.08769 5.92504 2.85296C5.47483 2.62153 4.97815 2.50102 4.47453 2.50102C3.9709 2.50102 3.47423 2.62153 3.02402 2.85296C2.56072 3.08665 2.1744 3.45445 1.91282 3.91087Z" fill="#62c9ff"/></svg>' ),
			'wpmudev-videos'      => 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 10C20 11.8069 19.5327 13.4891 18.5981 15.0467C17.7259 16.5421 16.5109 17.757 14.9533 18.6916C13.4579 19.5639 11.8069 20 10 20C8.19315 20 6.5109 19.5639 4.95327 18.6916C3.45794 17.757 2.24299 16.5421 1.30841 15.0467C0.436137 13.4891 0 11.8069 0 10C0 8.19315 0.436137 6.54206 1.30841 5.04673C2.24299 3.4891 3.45794 2.27414 4.95327 1.40187C6.5109 0.46729 8.19315 0 10 0C11.8069 0 13.4579 0.46729 14.9533 1.40187C16.5109 2.27414 17.7259 3.4891 18.5981 5.04673C19.5327 6.54206 20 8.19315 20 10ZM17.9439 10C17.9439 8.50467 17.5389 7.13396 16.729 5.88785C15.9813 4.57944 14.9221 3.58255 13.5514 2.8972C12.243 2.21184 10.8411 1.93146 9.34579 2.05607C7.85047 2.18069 6.47975 2.67913 5.23364 3.5514L17.9439 10.9346V10ZM10 17.9439C11.6199 17.9439 13.1153 17.5078 14.486 16.6355C15.8567 15.7009 16.8536 14.4548 17.4766 12.8972L14.6729 11.3084L5.51402 16.5421C6.82243 17.4766 8.31776 17.9439 10 17.9439ZM3.92523 15.1402L7.00935 13.3645V6.91589L3.73832 5.04673C2.61682 6.47975 2.05607 8.13084 2.05607 10C2.05607 10.9346 2.21184 11.8692 2.52336 12.8037C2.83489 13.676 3.30218 14.4548 3.92523 15.1402ZM8.97196 12.243L12.7103 10.1869L8.97196 8.03738V12.243Z" fill="#62c9ff"/></svg>' )
		];

		$grouped_wpmudev_names = Menu_Manager::get_grouped_wpmudev_names();
		$grouped_wpmudev_icons = Menu_Manager::get_grouped_wpmudev_icons();

		foreach ( $all_plugins as $path => $data ) {
			$folder = dirname( $path );
			$is_xophz = strpos( $folder, 'xophz-compass' ) !== false;
			$is_wpmudev = in_array( $folder, $wpmudev_slugs, true );

			if ( $is_xophz || $is_wpmudev ) {
				$is_active = in_array( $path, $active_plugins, true ) || is_plugin_active_for_network( $path );
				$menu_slug = $folder_to_slug[ $folder ] ?? $folder;
				
				$icon = '';
				// Check for specific custom images first
				if ( $folder === 'xophz-compass-magic-formula' ) {
					$icon = plugins_url( 'xophz-compass/assets/magic-formula.svg' );
				} else {
					$icon_path = WP_PLUGIN_DIR . '/' . $folder . '/icon.svg';
					if ( file_exists( $icon_path ) ) {
						$icon = plugins_url( $folder . '/icon.svg' );
					}
				}

				$fallback_icon = plugins_url( 'assets/images/obsidian.png', dirname( __DIR__ ) . '/BlackBOX.php' );
				if ( $is_wpmudev && ! empty( $wpmudev_default_icons[ $folder ] ) ) {
					$fallback_icon = $wpmudev_default_icons[ $folder ];
				} elseif ( $is_wpmudev && ! empty( $grouped_wpmudev_icons[ $menu_slug ] ) ) {
					$fallback_icon = $grouped_wpmudev_icons[ $menu_slug ];
				}
				
				// If not found yet and it is a WPMUDEV plugin, use official branding icon or fallback
				if ( empty( $icon ) && $is_wpmudev ) {
					if ( ! empty( $wpmudev_branding_icons[ $folder ] ) ) {
						$icon = $wpmudev_branding_icons[ $folder ];
					} else {
						$icon = $fallback_icon;
					}
				}
				
				if ( empty($icon) ) {
					$icon = $fallback_icon;
				}

				// Resolve themed names
				$name = $data['Name'];
				if ( $is_xophz && class_exists( '\Xophz_Compass_Branding' ) ) {
					$slug = str_replace( 'xophz-compass-', '', $folder );
					if ( $slug === 'compass' ) {
						$slug = 'compass';
					}
					$default_name = trim( str_replace( 'Xophz', '', $data['Name'] ) );
					$name = \Xophz_Compass_Branding::get_plugin_name( $slug, $default_name );
				} else if ( $is_wpmudev ) {
					if ( isset( $grouped_wpmudev_names[ $menu_slug ] ) ) {
						$name = $grouped_wpmudev_names[ $menu_slug ];
					} else {
						$name = str_replace( 'WPMU DEV', '', $data['Name'] );
						$name = trim( $name, ' -' );
					}
				}

				// Determine the Go URL
				$go_url = '';
				if ( $is_xophz ) {
					$route_map = [
						'quests' => 'questbook',
						'alphabet-soup' => 'newsroom'
					];
					$route_slug = $route_map[$slug] ?? $slug;
					
					if ( $slug === 'compass' ) {
						$go_url = admin_url( 'admin.php?page=xophz-compass' );
					} else {
						$go_url = admin_url( 'admin.php?page=xophz-compass#/' . $route_slug );
					}
				} else if ( $is_wpmudev ) {
					$go_url = admin_url( 'admin.php?page=' . $menu_slug );
				}

				$val = self::$valuations[ $folder ] ?? null;
				$annual_value = $val ? $val[0] : 0;
				$replaces_saas = $val ? $val[1] : '';
				$rationale = $val ? ($val[2] ?? '') : '';
				$min_tier = $val ? ($val[3] ?? ($is_xophz ? 'quantum' : 'bronze')) : ($is_xophz ? 'quantum' : 'bronze');
				$standalone_price = $val ? ($val[4] ?? 79) : 79;
				$comp_type = $val ? ($val[5] ?? ($is_xophz ? 'portal' : 'infrastructure')) : ($is_xophz ? 'portal' : 'infrastructure');

				$tiers_map = self::get_tesseract_tiers();
				$tier_info = $tiers_map[ $min_tier ] ?? [
					'id'    => $min_tier,
					'name'  => ucfirst( str_replace( '-', ' ', $min_tier ) ),
					'color' => '#62c9ff',
					'track' => 'micro',
					'price' => '$14.99/mo',
				];

				$display_plugins[] = [
					'name'             => $name,
					'desc'             => $data['Description'],
					'version'          => $data['Version'],
					'author'           => $data['Author'],
					'active'           => $is_active,
					'icon'             => $icon,
					'fallback_icon'    => $fallback_icon,
					'type'             => $is_xophz ? 'Compass Engine' : 'Infrastructure',
					'comp_type'        => $comp_type,
					'min_tier'         => $min_tier,
					'tier_name'        => $tier_info['name'],
					'tier_icon'        => $tier_info['icon'] ?? 'fad fa-layer-group',
					'tier_color'       => $tier_info['color'],
					'tier_track'       => $tier_info['track'],
					'tier_price'       => $tier_info['price'],
					'standalone_price' => $standalone_price,
					'path'             => $path,
					'slug'             => $folder,
					'go_url'           => $go_url,
					'value'            => $annual_value,
					'replaces'         => $replaces_saas,
					'rationale'        => $rationale
				];
			}
		}

		usort( $display_plugins, function($a, $b) {
			return strcasecmp( $a['name'], $b['name'] );
		});

		$compass_plugins = array_filter( $display_plugins, function($p) { return $p['type'] === 'Compass Engine'; } );
		$infrastructure_plugins = array_filter( $display_plugins, function($p) { return $p['type'] === 'Infrastructure'; } );

		$total_value = array_sum( array_column( $display_plugins, 'value' ) );
		$active_value = array_sum( array_map(
			function($p) { return $p['active'] ? $p['value'] : 0; },
			$display_plugins
		));
		$total_standalone_value = array_sum( array_column( $display_plugins, 'standalone_price' ) );
		$active_count = count( array_filter( $display_plugins, function($p) { return $p['active']; } ) );
		$total_count = count( $display_plugins );

		$tesseract_tiers = self::get_tesseract_tiers();
		$obsidian_icon_url = plugins_url( 'assets/images/obsidian.png', dirname( __DIR__ ) . '/BlackBOX.php' );

		include __DIR__ . '/templates/dashboard.php';
	}

	public function register_rest_routes() {
		register_rest_route( 'blackbox/v1', '/toggle', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'rest_toggle_plugin' ],
			'permission_callback' => function() {
				return current_user_can( 'activate_plugins' );
			},
			'args'                => [
				'plugin' => [
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				],
				'toggle' => [
					'required'          => true,
					'type'              => 'string',
					'enum'              => [ 'activate', 'deactivate' ],
					'sanitize_callback' => 'sanitize_text_field',
				],
			],
		] );
	}

	public function rest_toggle_plugin( \WP_REST_Request $request ) {
		$plugin = $request->get_param( 'plugin' );
		$action = $request->get_param( 'toggle' );

		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( $action === 'activate' ) {
			$result = activate_plugin( $plugin, '', false, true );
			if ( is_wp_error( $result ) ) {
				return new \WP_Error( 'activation_failed', $result->get_error_message(), [ 'status' => 400 ] );
			}
		} else {
			deactivate_plugins( $plugin, true );
		}

		return rest_ensure_response( [
			'success' => true,
			'status'  => $action,
			'plugin'  => $plugin,
		] );
	}

	public function ajax_toggle_plugin() {
		check_ajax_referer( 'blackbox_toggle', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';
		$action = isset( $_POST['toggle'] ) ? sanitize_text_field( wp_unslash( $_POST['toggle'] ) ) : '';

		if ( empty( $plugin ) || ! in_array( $action, [ 'activate', 'deactivate' ], true ) ) {
			wp_send_json_error( 'Invalid parameters' );
		}

		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( $action === 'activate' ) {
			$result = activate_plugin( $plugin, '', false, true );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result->get_error_message() );
			}
		} else {
			deactivate_plugins( $plugin, true );
		}

		wp_send_json_success( [
			'status' => $action,
			'plugin' => $plugin
		] );
	}
}
