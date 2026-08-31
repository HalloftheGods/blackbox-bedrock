<?php
/**
 * Settings partial - BlackBOX Bedrock Configuration
 *
 * Expected variables:
 *   $is_bedrock_disabled - bool
 *   $logo_url            - string URL to hallofthegodsinc.png
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap" style="position: relative; min-height: 80vh;">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php if ( $is_bedrock_disabled ) : ?>
		<div class="notice notice-warning inline" style="margin-top: 15px; margin-bottom: 20px; padding: 12px;">
			<p><strong>BlackBOX Bedrock is currently disabled.</strong> All theme modifications, custom menus, and background scripts are inactive.</p>
		</div>
	<?php endif; ?>
	<form action="options.php" method="post" style="position: relative; z-index: 2;">
		<?php
		settings_fields( 'xophz_compass_options_group' );
		do_settings_sections( 'xophz_compass_settings' );
		submit_button( 'Save Configuration' );
		?>
	</form>

	<div style="position: fixed; bottom: 20px; right: 20px; opacity: 0.05; pointer-events: none; z-index: 1;">
		<img src="<?php echo esc_url( $logo_url ); ?>" alt="Hall of the Gods Logo" style="width: 300px; height: auto;" />
	</div>
</div>
