<?php
/**
 * BlackBOX MU Framework
 * 
 * Modular Core Bootstrapper
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
