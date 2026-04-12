<?php
/**
 * BlackBOX Custom Error Template
 */
$logo_css = file_exists( dirname( __DIR__ ) . '/css/logo.css' ) ? file_get_contents( dirname( __DIR__ ) . '/css/logo.css' ) : '';
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
        body {
            background: var(--compass-bg) !important;
            background-attachment: fixed !important;
            background-size: cover !important;
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
            padding: 40px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            box-shadow: 0 16px 64px rgba(0, 0, 0, 0.5);
        }
        #error-container #logo {
            margin-bottom: 30px;
        }
        h1 {
            font-size: 24px;
            color: var(--hog-gold);
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body id="error-page">
    <div id="error-container">
        <div id="logo"></div>
        <h1><?php echo $title; ?></h1>
        <div class="error-message">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>
