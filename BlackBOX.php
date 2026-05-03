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
 * Version:           26.5.3.1097
 * Author:      Hall of the Gods, Inc.
 * Author URI:  https://www.hallofthegods.com/
 * Plugin URI:  https://github.com/HalloftheGods/blackbox-bedrock
 * Update URI:  https://github.com/HalloftheGods/blackbox-bedrock
 */

if ( ! defined( 'ABSPATH' ) ) exit;

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
