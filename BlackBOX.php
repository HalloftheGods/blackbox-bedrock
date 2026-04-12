<?php
/**
 * BlackBOX MU Framework
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class BlackBOX_MU_Core {
	
	public function __construct() {
		add_filter( 'wp_theme_json_data_theme', [ $this, 'override_editor_theme_json' ] );
		add_filter( 'block_editor_settings_all', [ $this, 'force_editor_css_settings' ], 9999, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_blackbox_styles' ], 9999 );
		add_action( 'admin_head', [ $this, 'enqueue_blackbox_styles' ], 9999 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_blackbox_styles' ], 9999 );
		add_action( 'admin_print_footer_scripts', [ $this, 'inject_canvas_script' ], 9999 );
		add_action( 'admin_head', [ $this, 'inject_iframe_class' ], 1 );
		
		add_action( 'login_enqueue_scripts', [ $this, 'enqueue_login_styles' ] );
		add_filter( 'login_headerurl', [ $this, 'custom_login_headerurl' ] );
		add_filter( 'login_headertext', [ $this, 'custom_login_headertext' ] );
		add_action( 'login_footer', [ $this, 'inject_canvas_script' ], 9999 );
	}

	public function inject_iframe_class() {
		$isIframe = isset( $_GET['compass_iframe'] ) && $_GET['compass_iframe'] === '1';
		if ( $isIframe ) {
			echo '<script>document.documentElement.classList.add("is-blackbox-iframe");</script>';
		} else {
			echo '<script>if (window.name === "blackbox-sub-app") { document.documentElement.classList.add("is-blackbox-iframe"); }</script>';
		}
	}

	public function override_editor_theme_json( $theme_json ) {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! $screen || ! $screen->is_block_editor() ) {
				return $theme_json;
			}
		}

		$bg_canvas = 'transparent';
		$text_main = '#f8f8f2';
		
		$new_data = array(
			'version' => 2,
			'styles' => array(
				'color' => array(
					'background' => $bg_canvas,
					'text'       => $text_main
				),
				'elements' => array(
					'link' => array(
						'color' => array( 'text' => '#62c9ff' )
					),
					'heading' => array(
						'color' => array( 'text' => $text_main )
					)
				)
			)
		);
		return $theme_json->update_with( $new_data );
	}

	public function force_editor_css_settings( $settings, $context ) {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! $screen || ! $screen->is_block_editor() ) {
				return $settings;
			}
		}

		$bg_canvas = 'transparent';
		$custom_css = '
			.editor-styles-wrapper, .is-root-container, .block-editor-writing-flow, body, html {
				background: ' . $bg_canvas . ' !important;
				background-color: ' . $bg_canvas . ' !important;
				color: #f8f8f2 !important;
			}
			.wp-block { color: #f8f8f2 !important; }
		';

		if ( ! isset( $settings['styles'] ) ) {
			$settings['styles'] = array();
		}
		$settings['styles'][] = array( 'css' => $custom_css );
		return $settings;
	}

	public function enqueue_blackbox_styles() {

		$base_css = file_get_contents( __DIR__ . '/css/base.css' );
		$wp_admin_css = file_get_contents( __DIR__ . '/css/wp-admin.css' );
		$sui_css = file_get_contents( __DIR__ . '/css/sui.css' );
		$iframe_css = file_get_contents( __DIR__ . '/css/iframe-mask.css' );
		
		$global_css = $base_css . $wp_admin_css . $sui_css . $iframe_css;

		if ( current_action() === 'enqueue_block_editor_assets' ) {
			$gutenberg_css = file_get_contents( __DIR__ . '/css/gutenberg.css' );
			$editor_css = $global_css . $gutenberg_css;
			wp_add_inline_style( 'wp-block-library', $editor_css );
			wp_add_inline_style( 'wp-edit-post', $editor_css );
		} else {
			echo '<style id="blackbox-global-admin">' . $global_css . '</style>';
		}
	}

	public function inject_canvas_script() {
		?>
		<script>
		(function() {
			// Do not inject the smoke canvas if we are inside a Compass sub-app iframe
			if (window.name === "blackbox-sub-app") return;
			
			if (window !== window.top && window.location.search.includes('theme=transparent')) {
				// We are in an iframe, and transparent!
			}
			if(document.getElementById('blackbox-smoke-canvas')) return;
			const canvas = document.createElement('canvas');
			canvas.id = 'blackbox-smoke-canvas';
			canvas.style.position = 'fixed';
			canvas.style.top = '0';
			canvas.style.left = '0';
			canvas.style.width = '100vw';
			canvas.style.height = '100vh';
			canvas.style.pointerEvents = 'none';
			canvas.style.zIndex = '0';
			canvas.style.opacity = '0.8';

			const waves = [
				{ y: 0.48, amplitude: 45, wavelength: 700, speed: 0.0002, offset: 0, color: "rgba(220, 230, 240, 0.20)", blur: 5, thickness: 100, scales: [1.0, 2.3, 0.7, 0.2], drifts: [1.0, 1.5, 0.8, 0.2], thickScales: [0.5, 1.2, 0.3], thickDrifts: [0.8, 0.4, 1.1] },
				{ y: 0.48, amplitude: 35, wavelength: 900, speed: -0.0004, offset: Math.PI, color: "rgba(200, 210, 220, 0.17)", blur: 5, thickness: 175, scales: [1.2, 1.8, 0.5, 0.3], drifts: [0.9, 1.7, 0.6, 0.4], thickScales: [0.7, 0.9, 0.4], thickDrifts: [1.2, 0.5, 0.8] },
				{ y: 0.55, amplitude: 28, wavelength: 1200, speed: 0.0004, offset: Math.PI / 2, color: "rgba(255, 255, 255, 0.55)", blur: 3, thickness: 15, scales: [0.8, 2.7, 1.1, 0.1], drifts: [1.1, 1.3, 1.2, 0.1], thickScales: [0.4, 1.5, 0.2], thickDrifts: [0.6, 0.9, 1.3], isRibbon: true },
				{ y: 0.48, amplitude: 40, wavelength: 800, speed: -0.0003, offset: Math.PI * 1.5, color: "rgba(180, 190, 200, 0.17)", blur: 5, thickness: 250, scales: [1.5, 1.2, 0.9, 0.4], drifts: [0.7, 2.0, 0.5, 0.3], thickScales: [0.6, 0.8, 0.5], thickDrifts: [1.0, 0.3, 0.7] }
			];

			const particles = [];
			const maxParticles = 160;

			function createParticle(x, y) {
				return { x, orbitRadiusOffset: (Math.random() - 0.5) * 35, orbitAngleOffset: (Math.random() - 0.5) * 0.5, size: Math.random() * 0.5 + 0.8, speedX: 0.15 + Math.random() * 0.5, flickerSpeed: Math.random() * 0.0005 + 0.002, flickerPhase: Math.random() * Math.PI * 2, orbitPhase: 0 };
			}

			function getWaveY(wave, x, time, height) {
				const baseline = height * wave.y;
				const t = time * wave.speed + wave.offset;
				const relX = x / wave.wavelength;
				const w1 = Math.sin(relX * wave.scales[0] + t * wave.drifts[0]);
				const w2 = Math.sin(relX * wave.scales[1] + t * wave.drifts[1]) * 0.5;
				const w3 = Math.sin(relX * wave.scales[2] - t * wave.drifts[2]) * 0.3;
				const w4 = Math.sin(t * wave.scales[3]) * wave.drifts[3];
				return baseline + (w1 + w2 + w3 + w4) * wave.amplitude;
			}
			
			function getWaveThickness(wave, x, time) {
				const t = time * wave.speed + wave.offset;
				const relX = x / wave.wavelength;
				const th1 = Math.sin(relX * wave.thickScales[0] + t * wave.thickDrifts[0]);
				const th2 = Math.sin(relX * wave.thickScales[1] + t * wave.thickDrifts[1]) * 0.4;
				const th3 = Math.sin(t * wave.thickScales[2]) * wave.thickDrifts[2];
				return wave.thickness + (th1 + th2 + th3) * (wave.amplitude * 0.6);
			}

			function initCanvas() {
				// Don't inject if visual editor wrapper isn't present
				document.body.prepend(canvas);
				const ctx = canvas.getContext("2d");

				function resize() {
					canvas.width = window.innerWidth;
					canvas.height = window.innerHeight;
				}
				window.addEventListener("resize", resize);
				resize();

				const half = maxParticles / 2;
				const step = canvas.width / half;
				for (let i = 0; i < half; i++) {
					const x = i * step;
					particles.push(createParticle(x, 0));
					const p2 = createParticle(x, 0);
					p2.speedX *= -1;
					p2.orbitPhase = Math.PI;
					particles.push(p2);
				}

				function animate(time) {
					if(!ctx) return;
					ctx.clearRect(0, 0, canvas.width, canvas.height);
					
					waves.forEach(wave => {
						const baseline = canvas.height * wave.y;
						ctx.beginPath();
						ctx.moveTo(0, baseline);
						for (let x = 0; x <= canvas.width; x += 10) {
							ctx.lineTo(x, getWaveY(wave, x, time, canvas.height));
						}
						for (let x = canvas.width; x >= 0; x -= 10) {
							const t = time * wave.speed + wave.offset;
							const th1 = Math.sin((x / wave.wavelength) * wave.thickScales[0] + t * wave.thickDrifts[0]);
							const th2 = Math.sin((x / wave.wavelength) * wave.thickScales[1] + t * wave.thickDrifts[1]) * 0.4;
							const th3 = Math.sin(t * wave.thickScales[2]) * wave.thickDrifts[2];
							const currentThickness = wave.thickness + (th1 + th2 + th3) * (wave.amplitude * 0.6);
							ctx.lineTo(x, getWaveY(wave, x, time, canvas.height) + currentThickness);
						}
						ctx.closePath();
						
						const grad = ctx.createLinearGradient(0, 0, canvas.width, 0);
						grad.addColorStop(0, "rgba(200, 210, 220, 0.05)");
						grad.addColorStop(0.2, "rgba(215, 225, 235, 0.10)");
						grad.addColorStop(0.4, "rgba(215, 225, 235, 0.18)");
						grad.addColorStop(0.75, wave.color);
						grad.addColorStop(1, "rgba(200, 210, 220, 0.05)");
						ctx.fillStyle = grad;
						if (wave.blur > 0) ctx.filter = `blur(${wave.blur}px)`;
						ctx.fill();
						ctx.filter = "none";
					});

					const ribbonWave = waves.find(w => w.isRibbon);
					if (ribbonWave) {
						ctx.save();
						ctx.shadowBlur = 1;
						ctx.shadowColor = "white";
						for (let i = 0; i < particles.length; i++) {
							const p = particles[i];
							p.x += p.speedX;
							if (p.x > canvas.width) p.x = 0;
							if (p.x < 0) p.x = canvas.width;
							const finalAngle = (p.x * 0.008 + time * 0.0003 + p.orbitPhase) + p.orbitAngleOffset;
							const volThick = getWaveThickness(ribbonWave, p.x, time);
							const centerY = getWaveY(ribbonWave, p.x, time, canvas.height) + volThick / 2;
							const currentY = centerY + Math.sin(finalAngle) * (volThick / 2 + 55 + p.orbitRadiusOffset);
							const depthFactor = (Math.cos(finalAngle) + 1) / 2;
							const flicker = 0.5 + Math.sin(time * p.flickerSpeed + p.flickerPhase) * 0.5;
							ctx.beginPath();
							ctx.arc(p.x, currentY, p.size * (0.7 + depthFactor * 0.6), 0, Math.PI * 2);
							ctx.fillStyle = `rgba(255, 255, 255, ${(depthFactor * 0.7 + 0.3) * (0.8 + flicker * 0.2)})`;
							ctx.fill();
						}
						ctx.restore();
					}
					requestAnimationFrame(animate);
				}
				requestAnimationFrame(animate);
			}

			if (document.readyState === "complete" || document.readyState === "interactive") {
				initCanvas();
			} else {
				document.addEventListener("DOMContentLoaded", initCanvas);
			}
		})();
		</script>
		<?php
	}

	public function enqueue_login_styles() {
		$logo_url = content_url('plugins/xophz-compass/admin/dist/omega-logox300.png');
		echo '<style type="text/css">
			:root {
				--hog-gold: #d9be6f;
				--hog-gold-hover: #e6cd82;
				--hog-gold-dark: #c0a455;
				--hog-text: #1a1a1a;
				--compass-bg: radial-gradient(farthest-corner circle at 0% 0%, #2271b1 0%, #1a1e22 100%);
				--rough-glass-bg: linear-gradient(135deg, rgba(13, 17, 23, 0.6), rgba(13, 17, 23, 0.2));
				--rough-glass-border: rgba(90, 105, 172, 0.301);
				--rough-glass-filter: blur(20px) saturate(150%);
				--text-main: #f8f8f2;
			}
			/* Background Overlay */
			body.login {
				background: var(--compass-bg) !important;
				background-attachment: fixed !important;
				background-size: cover !important;
			}
			/* Logo */
			#login h1 a, .login h1 a {
				background-image: url("' . $logo_url . '") !important;
				background-repeat: no-repeat;
				background-position: center !important;
				background-size: contain !important;
				width: 100% !important;
				height: 140px !important;
			}
			/* Glass Options */
			.login #loginform, .login #registerform, .login #lostpasswordform {
				background: var(--rough-glass-bg) !important;
				backdrop-filter: var(--rough-glass-filter) !important;
				border: 1px solid var(--rough-glass-border) !important;
				box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3) !important;
				border-radius: 8px !important;
				padding: 26px 24px 46px !important;
			}
			/* Message & Notice Blocks */
			.login .message, .login .success, .login #login_error {
				background: var(--rough-glass-bg) !important;
				backdrop-filter: var(--rough-glass-filter) !important;
				border: 1px solid var(--rough-glass-border) !important;
				border-left: 4px solid var(--hog-gold) !important;
				box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3) !important;
				border-radius: 8px !important;
				color: var(--text-main) !important;
			}
			/* Typography */
			.login label, .login .privacy-policy-page-link > a {
				color: var(--text-main) !important;
			}
			.login #nav, .login #backtoblog {
				text-shadow: 0 1px 2px rgba(0,0,0,0.8);
			}
			/* Inputs */
			.login input[type="text"],
			.login input[type="password"],
			.login input[type="email"] {
				background: rgba(0, 0, 0, 0.3) !important;
				color: var(--text-main) !important;
				border: 1px solid rgba(255, 255, 255, 0.1) !important;
				border-radius: 4px !important;
				box-shadow: none !important;
			}
			/* Inputs and Focus States */
			.login input[type="text"]:focus,
			.login input[type="password"]:focus,
			.login input[type="checkbox"]:focus {
				border-color: var(--hog-gold) !important;
				box-shadow: 0 0 0 1px var(--hog-gold) !important;
				background: rgba(0, 0, 0, 0.5) !important;
			}
			/* Autofill Override */
			.login input:-webkit-autofill,
			.login input:-webkit-autofill:hover, 
			.login input:-webkit-autofill:focus, 
			.login input:-webkit-autofill:active {
				transition: background-color 5000s ease-in-out 0s !important;
				-webkit-text-fill-color: var(--text-main) !important;
			}
			/* Checkbox Mark */
			.login input[type="checkbox"] {
				background: rgba(0, 0, 0, 0.3) !important;
				border: 1px solid rgba(255, 255, 255, 0.1) !important;
			}
			.login input[type="checkbox"]:checked::before {
				content: url("data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M14.83%204.89l1.34.94-5.81%208.38H9.02L5.78%209.67l1.34-1.25%202.57%202.4z%27%20fill%3D%27%23d9be6f%27%2F%3E%3C%2Fsvg%3E") !important;
			}
			/* Submit Button */
			.wp-core-ui .button-primary {
				background: var(--hog-gold) !important;
				border-color: var(--hog-gold-dark) !important;
				color: var(--hog-text) !important;
				text-shadow: none !important;
				box-shadow: 0 1px 0 var(--hog-gold-dark) !important;
			}
			.wp-core-ui .button-primary:hover,
			.wp-core-ui .button-primary:focus,
			.wp-core-ui .button-primary:active {
				background: var(--hog-gold-hover) !important;
				border-color: var(--hog-gold-dark) !important;
				color: var(--hog-text) !important;
			}
			/* Links */
			.login #nav a, 
			.login #backtoblog a {
				color: var(--text-main) !important;
			}
			.login #backtoblog a:hover,
			.login #nav a:hover,
			.login h1 a:hover {
				color: var(--hog-gold) !important;
			}
			/* Password Eye Toggle */
			.login .wp-pwd button:hover span,
			.login .wp-pwd button:focus span {
				color: var(--hog-gold) !important;
			}
		</style>';
	}

	public function custom_login_headerurl() {
		return home_url();
	}

	public function custom_login_headertext() {
		return 'COMPASS Suite';
	}
}

new BlackBOX_MU_Core();
