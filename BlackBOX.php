<?php
/**
 * Plugin Name: BlackBOX MU Framework
 * Description: Modular Core Bootstrapper
 * Version: 1.0.0
 * Author: Hall of the Gods, Inc.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load Core Utilities First
require_once __DIR__ . '/includes/Core.php';

// Load Modules
require_once __DIR__ . '/admin/Admin.php';
require_once __DIR__ . '/public/PublicFace.php';
require_once __DIR__ . '/error/Error.php';

// Initialize
new \BlackBOX\Admin();
new \BlackBOX\PublicFace();
new \BlackBOX\Error();
