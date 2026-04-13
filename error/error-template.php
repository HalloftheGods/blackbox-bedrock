<?php
/**
 * BlackBOX Custom Error Template
 */
$logo_css = file_exists( dirname( __DIR__ ) . '/assets/css/logo.css' ) ? file_get_contents( dirname( __DIR__ ) . '/assets/css/logo.css' ) : '';
$smoke_js = file_exists( dirname( __DIR__ ) . '/assets/js/smoke-canvas.js' ) ? file_get_contents( dirname( __DIR__ ) . '/assets/js/smoke-canvas.js' ) : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <style type="text/css">
        <?php echo $logo_css; ?>
        :root {
            --hog-gold: #d9be6f;
            --compass-bg: radial-gradient(farthest-corner circle at 0% 0%, #2271b1 0%, #1a1e22 100%);
            --rough-glass-bg: linear-gradient(135deg, rgba(13, 17, 23, 0.8), rgba(13, 17, 23, 0.4));
            --rough-glass-border: rgba(90, 105, 172, 0.3);
            --rough-glass-filter: blur(20px) saturate(150%);
            --text-main: #f8f8f2;
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        #error-container {
            background: var(--rough-glass-bg);
            backdrop-filter: var(--rough-glass-filter);
            -webkit-backdrop-filter: var(--rough-glass-filter);
            border: 1px solid var(--rough-glass-border);
            border-radius: 12px;
            padding: 32px 32px 40px;
            max-width: 460px;
            width: 90%;
            text-align: center;
            box-shadow: 0 16px 64px rgba(0, 0, 0, 0.5);
        }
        #error-container #logo {
            background-image: var(--custom-logo-base64) !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            width: 100% !important;
            height: 120px !important;
            margin: 0 auto 20px !important;
            display: block !important;
            border: none !important;
            background-color: transparent !important;
        }
        .error-message {
            font-size: 16px;
            line-height: 1.6;
        }
        .error-message p:first-child {
            font-size: 20px;
            color: var(--hog-gold);
            margin-bottom: 15px;
            margin-top: 0;
            font-weight: 500;
        }
        .error-message p {
            margin-bottom: 15px;
        }
        .error-message p:last-child {
            margin-bottom: 0;
        }
        a {
            color: var(--hog-gold);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        a:hover, a:focus {
            color: #62c9ff;
        }
    </style>
</head>
<body id="error-page">
    <div id="error-container">
        <div id="logo"></div>
        <div class="error-message">
            <?php echo $message; ?>
        </div>
    </div>
    <script><?php echo $smoke_js; ?></script>
</body>
</html>
