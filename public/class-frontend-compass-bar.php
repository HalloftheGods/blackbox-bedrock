<?php
namespace BlackBOX\PublicFace;

if ( ! defined( 'ABSPATH' ) ) exit;

class Frontend_Compass_Bar {

	const OPTION_KEY = 'blackbox_bedrock_frontend_compass_bar';
	const AJAX_ACTION = 'blackbox_toggle_frontend_bar';
	const NONCE_ACTION = 'blackbox_frontend_bar_nonce';

	/**
	 * Harvested admin bar nodes ready for frontend JSON serialization.
	 *
	 * @var array
	 */
	private $harvested_nodes = [];

	/**
	 * Contextual quick links extracted from the live WP Admin Bar.
	 *
	 * @var array
	 */
	private $quick_links = [];

	public function __construct() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'ajax_toggle_bar' ] );

		if ( is_admin() ) {
			return;
		}

		add_action( 'wp', [ $this, 'bootstrap_frontend' ], 20 );
	}

	/**
	 * Check whether the Compass Frontend Bar should replace the native WP admin bar.
	 *
	 * @return bool
	 */
	public function should_activate() {
		if ( ! is_user_logged_in() || ! is_admin_bar_showing() ) {
			return false;
		}

		if ( isset( $_GET['native_bar'] ) && $_GET['native_bar'] === '1' ) {
			return false;
		}

		$enabled = get_option( self::OPTION_KEY, '1' );
		return ( $enabled === '1' || $enabled === 1 || $enabled === true );
	}

	/**
	 * Register frontend hooks once WordPress query and admin bar state are ready.
	 */
	public function bootstrap_frontend() {
		if ( ! $this->should_activate() ) {
			return;
		}

		add_action( 'wp_head', [ $this, 'output_native_bar_suppression' ], 9999 );
		add_action( 'wp_before_admin_bar_render', [ $this, 'harvest_admin_bar_nodes' ], 9999 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 9999 );
		add_action( 'wp_footer', [ $this, 'render_mount_point' ], 9999 );
	}

	/**
	 * Hide native #wpadminbar and remove top margin gap while preserving node hooks.
	 */
	public function output_native_bar_suppression() {
		echo '<style id="blackbox-compass-bar-reset">'
			. '#wpadminbar{display:none!important;}'
			. 'html{margin-top:0!important;}'
			. '* html body{margin-top:0!important;}'
			. 'body{padding-bottom:calc(64px + env(safe-area-inset-bottom,0px))!important;}'
			. '@media screen and (max-width:782px){html{margin-top:0!important;}}'
			. '</style>' . "\n";
	}

	/**
	 * Enqueue the Shadow DOM frontend dock script.
	 */
	public function enqueue_assets() {
		$assets_url = defined( 'WPMU_PLUGIN_URL' )
			? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets'
			: content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$script_file = dirname( __DIR__ ) . '/assets/js/frontend-compass-bar.js';
		$version     = file_exists( $script_file ) ? (string) filemtime( $script_file ) : '1.0.0';

		wp_enqueue_script(
			'blackbox-frontend-compass-bar',
			$assets_url . '/js/frontend-compass-bar.js',
			[],
			$version,
			true
		);
	}

	/**
	 * Snapshot all registered WP_Admin_Bar nodes into a clean nested tree.
	 */
	public function harvest_admin_bar_nodes() {
		global $wp_admin_bar;

		if ( ! is_object( $wp_admin_bar ) || ! method_exists( $wp_admin_bar, 'get_nodes' ) ) {
			return;
		}

		$raw_nodes = $wp_admin_bar->get_nodes();
		if ( empty( $raw_nodes ) || ! is_array( $raw_nodes ) ) {
			return;
		}

		$this->extract_quick_links( $raw_nodes );
		$this->harvested_nodes = $this->build_node_tree( $raw_nodes );
	}

	/**
	 * Extract contextual Edit, Customize, and + New links for bottom nav slots.
	 *
	 * @param array $raw_nodes
	 */
	private function extract_quick_links( $raw_nodes ) {
		if ( isset( $raw_nodes['edit'] ) && ! empty( $raw_nodes['edit']->href ) ) {
			$this->quick_links['editUrl']   = esc_url_raw( $raw_nodes['edit']->href );
			$this->quick_links['editLabel'] = $this->clean_node_title( $raw_nodes['edit']->title ) ?: __( 'Edit', 'blackbox-bedrock' );
		}

		if ( isset( $raw_nodes['customize'] ) && ! empty( $raw_nodes['customize']->href ) ) {
			$this->quick_links['customizeUrl'] = esc_url_raw( $raw_nodes['customize']->href );
		}

		$new_items = [];
		foreach ( $raw_nodes as $node ) {
			if ( ! empty( $node->group ) ) {
				continue;
			}
			if ( $node->parent === 'new-content' && ! empty( $node->href ) ) {
				$title = $this->clean_node_title( $node->title );
				if ( $title !== '' ) {
					$new_items[] = [
						'id'    => (string) $node->id,
						'title' => $title,
						'href'  => esc_url_raw( $node->href ),
					];
				}
			}
		}
		$this->quick_links['newItems'] = $new_items;
	}

	/**
	 * Convert flat WP_Admin_Bar nodes into a clean nested tree, flattening WP group containers.
	 *
	 * @param array $raw_nodes
	 * @return array
	 */
	private function build_node_tree( $raw_nodes ) {
		$skip_ids = [
			'menu-toggle',
			'search',
			'my-account',
			'user-actions',
			'user-info',
			'edit-profile',
			'logout',
		];

		$children_by_parent = [];
		foreach ( $raw_nodes as $id => $node ) {
			if ( in_array( $id, $skip_ids, true ) ) {
				continue;
			}

			$parent_id = $this->resolve_visible_parent( $node->parent, $raw_nodes, $skip_ids );
			if ( $parent_id === false ) {
				continue;
			}

			$children_by_parent[ $parent_id ][] = $node;
		}

		return $this->serialize_branch( '', $children_by_parent, 0 );
	}

	/**
	 * Walk upward past WP group wrappers to find the nearest visible parent ID.
	 *
	 * @param string|false $parent_id
	 * @param array        $raw_nodes
	 * @param array        $skip_ids
	 * @return string|false
	 */
	private function resolve_visible_parent( $parent_id, $raw_nodes, $skip_ids ) {
		$visited = [];
		$current = is_string( $parent_id ) ? $parent_id : '';

		while ( $current !== '' ) {
			if ( isset( $visited[ $current ] ) ) {
				return false;
			}
			$visited[ $current ] = true;

			if ( in_array( $current, $skip_ids, true ) ) {
				return false;
			}

			if ( $current === 'top-secondary' || $current === 'root-default' ) {
				return '';
			}

			if ( ! isset( $raw_nodes[ $current ] ) ) {
				return '';
			}

			$parent_node = $raw_nodes[ $current ];
			if ( ! empty( $parent_node->group ) ) {
				$current = is_string( $parent_node->parent ) ? $parent_node->parent : '';
				continue;
			}

			return $current;
		}

		return '';
	}

	/**
	 * Recursively serialize a branch of admin bar nodes.
	 *
	 * @param string $parent_id
	 * @param array  $children_by_parent
	 * @param int    $depth
	 * @return array
	 */
	private function serialize_branch( $parent_id, $children_by_parent, $depth ) {
		if ( $depth > 4 || empty( $children_by_parent[ $parent_id ] ) ) {
			return [];
		}

		$result = [];
		foreach ( $children_by_parent[ $parent_id ] as $node ) {
			if ( ! empty( $node->group ) ) {
				continue;
			}

			$title    = $this->clean_node_title( $node->title );
			$href     = ! empty( $node->href ) ? esc_url_raw( $node->href ) : '';
			$children = $this->serialize_branch( (string) $node->id, $children_by_parent, $depth + 1 );

			if ( $title === '' && empty( $children ) ) {
				continue;
			}

			if ( $title === '' ) {
				$title = $this->humanize_id( (string) $node->id );
			}

			$target = '';
			if ( is_array( $node->meta ) && ! empty( $node->meta['target'] ) ) {
				$target = sanitize_text_field( (string) $node->meta['target'] );
			}

			$result[] = [
				'id'       => (string) $node->id,
				'title'    => $title,
				'href'     => $href,
				'target'   => $target,
				'children' => $children,
			];
		}

		return $result;
	}

	/**
	 * Strip HTML/screen-reader-only markup from a WP admin bar node title.
	 *
	 * @param mixed $raw_title
	 * @return string
	 */
	private function clean_node_title( $raw_title ) {
		if ( ! is_string( $raw_title ) || $raw_title === '' ) {
			return '';
		}

		$cleaned = preg_replace( '#<span[^>]*class=["\'][^"\']*screen-reader-text[^"\']*["\'][^>]*>.*?</span>#is', '', $raw_title );
		$cleaned = wp_strip_all_tags( (string) $cleaned );
		$cleaned = html_entity_decode( $cleaned, ENT_QUOTES, 'UTF-8' );
		$cleaned = preg_replace( '/\s+/u', ' ', $cleaned );

		return trim( (string) $cleaned );
	}

	/**
	 * Fallback label from node ID when a node only rendered an icon.
	 *
	 * @param string $id
	 * @return string
	 */
	private function humanize_id( $id ) {
		$map = [
			'wp-logo'     => 'WordPress',
			'comments'    => 'Comments',
			'updates'     => 'Updates',
			'new-content' => 'New',
		];
		if ( isset( $map[ $id ] ) ) {
			return $map[ $id ];
		}
		return ucwords( str_replace( [ '-', '_' ], ' ', $id ) );
	}

	/**
	 * Output the mount container and JSON payload in wp_footer.
	 */
	public function render_mount_point() {
		$user = wp_get_current_user();
		if ( ! $user || ! $user->exists() ) {
			return;
		}

		$roles    = is_array( $user->roles ) ? $user->roles : [];
		$role     = ! empty( $roles ) ? ucfirst( str_replace( '_', ' ', $roles[0] ) ) : $user->user_login;
		$subtitle = is_super_admin( $user->ID ) ? 'Super Admin' : $role;

		$payload = [
			'user'             => [
				'displayName' => $user->display_name ?: $user->user_login,
				'subtitle'    => $subtitle,
				'avatarUrl'   => get_avatar_url( $user->ID, [ 'size' => 72 ] ),
				'profileUrl'  => get_edit_profile_url( $user->ID ),
				'logoutUrl'   => wp_logout_url( home_url( add_query_arg( null, null ) ) ),
			],
			'siteName'         => get_bloginfo( 'name' ) ?: 'My Site',
			'urls'             => [
				'compass'      => admin_url( 'admin.php?page=xophz-compass#/' ),
				'wpAdmin'      => admin_url(),
				'editUrl'      => $this->quick_links['editUrl'] ?? '',
				'editLabel'    => $this->quick_links['editLabel'] ?? __( 'Edit', 'blackbox-bedrock' ),
				'customizeUrl' => $this->quick_links['customizeUrl'] ?? '',
				'nativeBarUrl' => add_query_arg( 'native_bar', '1' ),
			],
			'newItems'         => $this->quick_links['newItems'] ?? [],
			'nodes'            => $this->harvested_nodes,
			'canManageOptions' => current_user_can( 'manage_options' ),
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( self::NONCE_ACTION ),
		];

		echo '<div id="blackbox-compass-bar-root"></div>' . "\n";
		echo '<script type="application/json" id="blackbox-compass-bar-data">'
			. wp_json_encode( $payload )
			. '</script>' . "\n";
	}

	/**
	 * AJAX handler to toggle the frontend Compass bar option.
	 */
	public function ajax_toggle_bar() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Insufficient permissions' ], 403 );
		}

		$enable = isset( $_POST['enable'] ) && $_POST['enable'] === '1' ? '1' : '0';
		update_option( self::OPTION_KEY, $enable );

		wp_send_json_success( [ 'enabled' => $enable === '1' ] );
	}
}
