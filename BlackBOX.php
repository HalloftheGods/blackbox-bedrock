<?php
/**
 * Plugin Name: BlackBOX MU Framework
 * Description: Modular Core Bootstrapper
 * Version:           26.4.22.352
 * Author: Hall of the Gods, Inc.
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
