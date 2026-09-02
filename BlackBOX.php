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
 * Version:           26.9.2-171
 * Author:      Hall of the Gods, Inc.
 * Author URI:  https://www.hallofthegods.com/
 * Plugin URI:  https://github.com/HalloftheGods/blackbox-bedrock
 * Update URI:  https://github.com/HalloftheGods/blackbox-bedrock
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Master constant kill-switch via wp-config.php
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

