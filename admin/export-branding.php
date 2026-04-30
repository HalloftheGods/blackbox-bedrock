<?php
/**
 * Utility script to export all Compass and Branda branding settings to an SQL file.
 * This file can be visited directly by an authenticated admin to generate and download the .sql payload.
 */

// Load WordPress context
$wp_load = realpath( __DIR__ . '/../../../../../wp-load.php' );
if ( ! file_exists( $wp_load ) ) {
    die( "Cannot find wp-load.php" );
}
require_once $wp_load;

// Ensure only admins can execute this
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Unauthorized' );
}

global $wpdb;

// Target option keys that hold our unified branding setup
$query = "
    SELECT option_name, option_value 
    FROM {$wpdb->options} 
    WHERE option_name = 'compass_branding' 
       OR option_name LIKE 'ub_%' 
       OR option_name LIKE 'wpmudev_branding%'
";
$results = $wpdb->get_results( $query );

if ( empty( $results ) ) {
    wp_die( 'No branding options found to export.' );
}

$sql = "-- =========================================================\n";
$sql .= "-- BlackBOX Bedrock Branding Deploy SQL\n";
$sql .= "-- Generated: " . gmdate('Y-m-d H:i:s') . " UTC\n";
$sql .= "-- =========================================================\n\n";

$sql .= "INSERT INTO `{$wpdb->options}` (`option_name`, `option_value`, `autoload`) VALUES\n";

$values = [];
foreach ( $results as $row ) {
    $values[] = sprintf(
        "('%s', '%s', 'yes')",
        esc_sql( $row->option_name ),
        esc_sql( $row->option_value )
    );
}

$sql .= implode( ",\n", $values ) . "\n";
$sql .= "ON DUPLICATE KEY UPDATE `option_value` = VALUES(`option_value`);\n";

// Serve as a downloadable SQL file
header( 'Content-Type: text/plain' );
header( 'Content-Disposition: attachment; filename="blackbox-branding-deploy.sql"' );
echo $sql;
exit;
