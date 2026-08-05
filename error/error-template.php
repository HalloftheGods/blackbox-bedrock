<?php
/**
 * BlackBOX Custom Error & Maintenance Template
 */
if ( ! headers_sent() ) {
	status_header( 503 );
	header( 'Retry-After: 3600' );
}

$logo_css = file_exists( dirname( __DIR__ ) . '/assets/css/logo.css' ) ? file_get_contents( dirname( __DIR__ ) . '/assets/css/logo.css' ) : '';
$smoke_js = file_exists( dirname( __DIR__ ) . '/assets/js/smoke-canvas.js' ) ? file_get_contents( dirname( __DIR__ ) . '/assets/js/smoke-canvas.js' ) : '';
$logo_url = function_exists( 'content_url' ) ? content_url( 'mu-plugins/blackbox-bedrock/assets/images/hallofthegodsinc.png' ) : '/wp-content/mu-plugins/blackbox-bedrock/assets/images/hallofthegodsinc.png';
?>
<!DOCTYPE html>
<html <?php if ( function_exists( 'language_attributes' ) ) language_attributes(); ?>>
<head>
    <meta charset="<?php echo function_exists( 'bloginfo' ) ? esc_attr( get_bloginfo( 'charset' ) ) : 'UTF-8'; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo ! empty( $title ) ? esc_html( $title ) : 'Scheduled Maintenance | Hall of the Gods'; ?></title>
    <style type="text/css">
        <?php echo $logo_css; ?>
        :root {
            --hog-gold: #d9be6f;
            --hog-cyan: #62c9ff;
            --compass-bg: radial-gradient(farthest-corner circle at 0% 0%, #0d1117 0%, #05070a 100%);
            --rough-glass-bg: linear-gradient(135deg, rgba(13, 17, 23, 0.85), rgba(20, 26, 38, 0.65));
            --rough-glass-border: rgba(90, 105, 172, 0.3);
            --rough-glass-filter: blur(20px) saturate(150%);
            --text-main: #f8f8f2;
            --text-muted: #8b949e;
        }
        html {
            background: var(--compass-bg) !important;
            background-attachment: fixed !important;
            background-size: cover !important;
            min-height: 100vh;
        }
        body {
            background: transparent !important;
            color: var(--text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }
        #error-container {
            position: relative;
            z-index: 10;
            background: var(--rough-glass-bg);
            backdrop-filter: var(--rough-glass-filter);
            -webkit-backdrop-filter: var(--rough-glass-filter);
            border: 1px solid var(--rough-glass-border);
            border-radius: 16px;
            padding: 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        #error-container #logo {
            background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            width: 100% !important;
            height: 110px !important;
            margin: 0 auto 24px !important;
            display: block !important;
            border: none !important;
            background-color: transparent !important;
        }
        .maintenance-badge {
            display: inline-block;
            background: rgba(217, 190, 111, 0.15);
            border: 1px solid rgba(217, 190, 111, 0.3);
            color: var(--hog-gold);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 20px;
        }
        .maintenance-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-main);
            margin: 0 0 12px 0;
            letter-spacing: -0.5px;
        }
        .maintenance-message {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 24px;
        }
        .status-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 14px 18px;
            font-size: 13px;
            color: var(--hog-cyan);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--hog-cyan);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--hog-cyan);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 0.4; transform: scale(0.9); }
            50% { opacity: 1; transform: scale(1.1); }
            100% { opacity: 0.4; transform: scale(0.9); }
        }
        a {
            color: var(--hog-gold);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        a:hover, a:focus {
            color: var(--hog-cyan);
        }
    </style>
</head>
<body id="error-page">
    <div id="error-container">
        <div id="logo"></div>
        <div class="maintenance-badge">System Notice</div>
        <h1 class="maintenance-title">Scheduled Maintenance</h1>
        <div class="maintenance-message">
            We are performing scheduled system updates to optimize performance and security. We will be back online shortly.
        </div>
        <div class="status-box">
            <span class="status-dot"></span>
            <span>Maintenance in progress</span>
        </div>
    </div>
    <script><?php echo $smoke_js; ?></script>
</body>
</html>
