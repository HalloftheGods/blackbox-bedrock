<?php
/**
██████╗ ██╗      █████╗  ██████╗██╗  ██╗██████╗  ██████╗ ██╗  ██╗
██╔══██╗██║     ██╔══██╗██╔════╝██║ ██╔╝██╔══██╗██╔═══██╗╚██╗██╔╝
██████╔╝██║     ███████║██║     █████╔╝ ██████╔╝██║   ██║ ╚███╔╝ 
██╔══██╗██║     ██╔══██║██║     ██╔═██╗ ██╔══██╗██║   ██║ ██╔██╗ 
██████╔╝███████╗██║  ██║╚██████╗██║  ██╗██████╔╝╚██████╔╝██╔╝ ██╗
╚═════╝ ╚══════╝╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═════╝  ╚═════╝ ╚═╝  ╚═╝                                                              
╔╗ ┌─┐┌┬┐┬─┐┌─┐┌─┐┬┌─
╠╩╗├┤  ││├┬┘│ ││  ├┴┐
╚═╝└─┘─┴┘┴└─└─┘└─┘┴ ┴
 * 
 * Plugin Name: BlackBOX Bedrock
 * Description: The modular bedrock foundation for the My Compass/YouMeOS Experience Engine.
 * Version:           26.9.6-521
 * Author:      Hall of the Gods, Inc.
 * Author URI:  https://www.hallofthegods.com/
 * Plugin URI:  https://github.com/HalloftheGods/blackbox-bedrock
 * Update URI:  https://github.com/HalloftheGods/blackbox-bedrock
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Detect native WordPress Site Editor (Full Site Editing)
$is_site_editor = false;

if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] === 'site-editor.php' ) {
	$is_site_editor = true;
} elseif ( isset( $_SERVER['SCRIPT_NAME'] ) && ( basename( $_SERVER['SCRIPT_NAME'] ) === 'site-editor.php' || strpos( $_SERVER['SCRIPT_NAME'], 'wp-admin/site-editor.php' ) !== false ) ) {
	$is_site_editor = true;
} elseif ( isset( $_SERVER['PHP_SELF'] ) && ( basename( $_SERVER['PHP_SELF'] ) === 'site-editor.php' || strpos( $_SERVER['PHP_SELF'], 'wp-admin/site-editor.php' ) !== false ) ) {
	$is_site_editor = true;
} elseif ( isset( $_SERVER['REQUEST_URI'] ) && ( strpos( $_SERVER['REQUEST_URI'], 'wp-admin/site-editor.php' ) !== false || strpos( $_SERVER['REQUEST_URI'], 'site-editor.php' ) !== false ) ) {
	$is_site_editor = true;
} elseif ( isset( $_GET['page'] ) && $_GET['page'] === 'gutenberg-edit-site' ) {
	$is_site_editor = true;
} elseif ( isset( $_GET['canvas'] ) && $_GET['canvas'] === 'edit' ) {
	$is_site_editor = true;
}

// Automatically activate master toggle when in native WP Site Editor to turn off Bedrock
if ( $is_site_editor && ! defined( 'BLACKBOX_BEDROCK_DISABLE' ) ) {
	define( 'BLACKBOX_BEDROCK_DISABLE', true );
}

// Master constant kill-switch via wp-config.php or runtime environment
if ( ( defined( 'BLACKBOX_BEDROCK_DISABLE' ) && BLACKBOX_BEDROCK_DISABLE ) ||
     ( defined( 'DISABLE_BLACKBOX_BEDROCK' ) && DISABLE_BLACKBOX_BEDROCK ) ||
     ( defined( 'BLACKBOX_DISABLE' ) && BLACKBOX_DISABLE ) ) {
	return;
}

// Load Core Utilities First
require_once __DIR__ . '/includes/Core.php';

// Load Modules
require_once __DIR__ . '/admin/class-blackbox-admin.php';
require_once __DIR__ . '/public/PublicFace.php';
require_once __DIR__ . '/error/Error.php';

// Initialize
new \BlackBOX\Admin();
new \BlackBOX\PublicFace();
new \BlackBOX\Error();

// Ensure pretty permalinks are active for REST API routes (/wp-json/)
add_action( 'init', function() {
	if ( empty( get_option( 'permalink_structure' ) ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
	}
}, 1 );

