<?php
namespace BlackBOX\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Settings {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_settings_assets' ] );
	}

	public function enqueue_settings_assets( $hook ) {
		$is_settings = ( isset( $_GET['page'] ) && in_array( $_GET['page'], [ 'w4-protocol', 'blackbox-settings' ], true ) );
		if ( ! $is_settings ) {
			return;
		}

		$assets_url = defined( 'WPMU_PLUGIN_URL' ) ? WPMU_PLUGIN_URL . '/blackbox-bedrock/assets' : content_url( 'mu-plugins/blackbox-bedrock/assets' );
		$assets_dir = dirname( __DIR__ ) . '/assets';

		wp_enqueue_style(
			'blackbox-settings',
			$assets_url . '/css/settings.css',
			[],
			filemtime( $assets_dir . '/css/settings.css' )
		);

		wp_enqueue_script(
			'blackbox-settings',
			$assets_url . '/js/settings.js',
			[],
			filemtime( $assets_dir . '/js/settings.js' ),
			true
		);
	}

	public function register_settings() {
		register_setting( 'xophz_compass_options_group', 'blackbox_bedrock_disabled' );
		register_setting( 'xophz_compass_options_group', 'xophz_compass_disable_mu_styles' );
		register_setting( 'xophz_compass_options_group', 'blackbox_bedrock_wp_admin_menu_2030' );

		add_settings_section(
			'xophz_compass_general_section',
			'',
			function() {},
			'xophz_compass_settings'
		);

		add_settings_field(
			'blackbox_bedrock_disabled',
			'Bedrock Plugin Status',
			function() {
				$val = get_option( 'blackbox_bedrock_disabled', '0' );
				$isActive = empty( $val );
				?>
				<div class="bb-toggle-container">
					<label class="bb-switch">
						<input type="hidden" name="blackbox_bedrock_disabled" value="1" />
						<input type="checkbox" name="blackbox_bedrock_disabled" value="0" class="bb-toggle-input" <?php checked( true, $isActive ); ?> />
						<span class="bb-slider"></span>
					</label>
					<span class="bb-status-label" data-active-text="Active" data-inactive-text="Disabled"><?php echo $isActive ? 'Active' : 'Disabled'; ?></span>
				</div>
				<p class="description" style="margin-top: 8px;">Master switch for the BlackBOX Bedrock foundation plugin. When disabled, all Bedrock modules and hooks halt. (Override via <code>define('BLACKBOX_BEDROCK_DISABLE', true);</code> in <code>wp-config.php</code>)</p>
				<?php
			},
			'xophz_compass_settings',
			'xophz_compass_general_section'
		);

		add_settings_field(
			'xophz_compass_disable_mu_styles',
			'2030 Lifestream Interface',
			function() {
				$val = get_option( 'xophz_compass_disable_mu_styles', '0' );
				$isActive = empty( $val );
				?>
				<div class="bb-toggle-container">
					<label class="bb-switch">
						<input type="hidden" name="xophz_compass_disable_mu_styles" value="1" />
						<input type="checkbox" name="xophz_compass_disable_mu_styles" value="0" class="bb-toggle-input" <?php checked( true, $isActive ); ?> />
						<span class="bb-slider"></span>
					</label>
					<span class="bb-status-label" data-active-text="Active" data-inactive-text="Disabled"><?php echo $isActive ? 'Active' : 'Disabled'; ?></span>
				</div>
				<p class="description" style="margin-top: 8px;">Apply Lifestream styling across all WP Admin pages. Disabling this turns off the dark theme, canvas background, color palette variables, and modal styling overrides.</p>
				<?php
			},
			'xophz_compass_settings',
			'xophz_compass_general_section'
		);

		add_settings_field(
			'blackbox_bedrock_wp_admin_menu_2030',
			'2030 BlackBOX Menu',
			function() {
				$val = get_option( 'blackbox_bedrock_wp_admin_menu_2030', '1' );
				$isEnabled = ( $val === '1' || $val === 1 );
				?>
				<div class="bb-toggle-container">
					<label class="bb-switch">
						<input type="hidden" name="blackbox_bedrock_wp_admin_menu_2030" value="0" />
						<input type="checkbox" name="blackbox_bedrock_wp_admin_menu_2030" value="1" class="bb-toggle-input" <?php checked( true, $isEnabled ); ?> />
						<span class="bb-slider"></span>
					</label>
					<span class="bb-status-label" data-active-text="Enabled" data-inactive-text="Disabled"><?php echo $isEnabled ? 'Enabled' : 'Disabled'; ?></span>
				</div>
				<p class="description" style="margin-top: 8px;">Enable 2030 menu accordion grouping and categorized sections for WPMUDEV and COMPASS plugins.</p>
				<?php
			},
			'xophz_compass_settings',
			'xophz_compass_general_section'
		);
	}

	public function settings_page_display() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$is_bedrock_disabled = ! empty( get_option( 'blackbox_bedrock_disabled' ) );
		$logo_url = content_url( 'mu-plugins/blackbox-bedrock/assets/images/hallofthegodsinc.png' );

		include __DIR__ . '/templates/settings.php';
	}
}


