# Changelog

All notable changes to the BlackBOX Bedrock submodule will be documented in this file.

## [2026-09-21]

### Fixed
- Fixed triple-nested scrollbars, concentric borders, and hardcoded 280px viewport cutoffs in Gutenberg popovers and dropdown menus (such as the header 3-dots "More options" menu). Disentangled `.components-popover__content`, `.components-dropdown-menu__menu`, and `.components-dropdown__content` from the WooCommerce listbox selector in `assets/css/wp-admin.css`.
- Removed `.components-dropdown-menu__menu` and `.components-dropdown__content` from the universal background stripper whitelist in `assets/css/wp-admin.css`, ensuring intermediate dropdown containers render as transparent zero-box passthroughs.
- Eliminated panel border soup in `assets/css/gutenberg.css` by stripping blanket 1px borders from intermediate sidebar elements (`.components-panel__body-title`, `.components-panel__row`, `.components-base-control`, `.components-button-group`, `.components-grid`, `.components-tools-panel`, `.color-block-support-panel`), enforcing clean accordion bottom dividers and reserving borders strictly for genuine input controls and discrete cards.

### Added
- Established the Single Surface Authority and Single Scroll Authority architecture for Gutenberg popovers and dropdown menus in `assets/css/gutenberg.css`:
  - Single surface: `.components-popover__content` rendered with dark glass (`rgba(13, 18, 29, 0.96)`), 24px blur, subtle cyan border (`rgba(98, 201, 255, 0.25)`), dynamic viewport constraint (`max-height: calc(100vh - 80px)`), and a dedicated 6px slender scrollbar.
  - Zero-box passthroughs: Inner wrappers (`.components-dropdown__content`, `.components-dropdown-menu__menu`, `.components-dropdown-menu`) stripped of borders, shadows, backgrounds, and redundant scrollbars.
  - Menu group hierarchy: Clean 1px dividers between groups, uppercase gold headers (`.components-menu-group__label`), full-width item buttons with cyan hover states, checkmark icon styling, and muted secondary description and shortcut text.

## [2026-09-18]

### Added
- Created dedicated Command Palette section stylesheet `assets/css/sections/commands.css` and enhanced `assets/css/modal-overrides.css`, applying Starship glassmorphic styling, neon cyan/gold accents, dark blurred backdrop overlays, and unified dark input styling to WordPress Core Command Palette (`.commands-command-menu`, `[cmdk-root]`).
- Created dedicated WP Defender section stylesheet `assets/css/sections/defender.css`, applying Starship frosted glass styling to sticky headers (`.defender-row--header`, `.defender-header`), sidebar activity log (`.defender-side-content-wrapper`), dashboard cards, slide panels, and feature status indicators.

### Fixed
- Fixed solid white background on WP Defender and WPMU DEV Core UI pages (`admin.php?page=wp-defender`, `wdf-hardening`, `wdf-scan`, etc.). Narrowed universal background stripper exclusions in `assets/css/wp-admin.css` to prevent blanket bypass on `.wpmudev-core-ui`, `.wpmudev-ui-root`, and `.defender-page` wrappers, adding explicit container transparency rules.
- Expanded WPMU DEV design token mappings in `assets/css/sui.css` to cover `--wpmudev-color-*` variants (`--wpmudev-color-gray-150`, `--wpmudev-color-green-25`, `--wpmudev-color-white`, etc.) and high-contrast `.wpmudev-core-ui__mono` scopes.
- Inverted and recolored hardcoded `#1A1A1A` and `#000000` SVG fills and strokes across WPMU DEV Core UI elements in `assets/css/sui.css` to guarantee contrast against dark glass surfaces.
- Fixed WP Admin spark iframe background stripping in `admin/class-admin-theme-styler.php`. Replaced blanket `HTTP_SEC_FETCH_DEST === 'iframe'` check with dual detection: server-side query parameters (`compass_iframe=wp-admin` or `wp_admin_frame=1`) and client window name (`window.name === 'wp-admin-frame'`). Assigns `is-wp-admin-frame` to preserve the Bedrock dark matrix background while reserving `is-compass-iframe` transparency masking strictly for Compass sub-apps.
- Added `is-wp-admin-frame` selector rules to `assets/css/iframe-mask.css` to hide native WordPress admin bar and menu without removing dark theme background or resetting `color-scheme`.
- Guarded `assets/js/smoke-canvas.js` to ensure the smoke canvas animation and dark theme are not disabled when running inside `window.name === 'wp-admin-frame'` or with `compass_iframe=wp-admin`.
- Fixed fatal `TypeError` in `render_error_template` (`error/Error.php`) where WordPress core fatal handlers passed a `WP_Error` instance to `wp_die()`. Extracted and joined error messages to guarantee string input before passing to `error-template.php` and `wp_kses_post()`.
- Added defensive `is_string` type check in `error/error-template.php` before `wp_kses_post()` execution to prevent `preg_replace` parameter type errors on PHP 8+.

## [2026-09-08]

### Added
- Created `Admin\Bar_Toggle` module (`admin/class-admin-bar-toggle.php`) and registered it in `Admin` (`admin/class-blackbox-admin.php`). Implements a native WordPress Admin Bar menu item in `#wp-admin-bar-top-secondary` adjacent to `#wp-admin-bar-my-account`, featuring native Dashicon (`dashicons-admin-appearance`), standard typography, hover styling, and dropdown submenu options (`BlackBOX Bedrock`, `Classic WordPress`, and `Bedrock Dashboard`) with instant 1-click toggle and authenticated AJAX synchronization (`wp_ajax_blackbox_toggle_theme`).
- Comprehensive root design token takeover in `assets/css/sui.css` overriding `--wpmudev-*` and `--sui-*` CSS custom properties on `:root`, `html`, `body`, `.sui-wrap`, `.wpmudev-core-ui`, and `.wpmudev-ui-root`. Maps surface foundations, typography contrast, grays, and borders into the Starship glass aesthetic.
- Component surface rules in `assets/css/sui.css` for modern WPMU DEV Core UI and WP Defender cards, panels, lists, inputs, and toggles (`[class*="wpmudev-card"]`, `.wpmudev-setting-card`, `[class*="defender-ar-"]`, `.defender-schedule-scan-pill`, `.defender-pill-toggle-group`), preventing blinding white backgrounds across Defender alerts and reports.
- Added exclusions for `.wpmudev-core-ui`, `.wpmudev-ui-root`, `[class*="wpmudev-"]`, `[class*="defender-"]`, `.defender-schedule-scan-pill`, and `.defender-pill-toggle-group` to the Universal Background Stripper in `assets/css/wp-admin.css`.

## [2026-09-07]

### Fixed
- Fixed stacking context and z-index ordering for SUI modals (`.sui-modal`, `.sui-dialog`) in `modal-overrides.css` and `sui.css`, ensuring `.sui-modal-overlay` sits behind `.sui-modal-content` and preventing the blurred backdrop mask from rendering over the modal window.
- Added `.sui-modal.sui-active` and `.sui-dialog.sui-active` to the fixed viewport centering layout rules so SUI modals properly expand full screen and vertically center in WPMU DEV plugins (Branda, Forminator, Hustle).

## [2026-09-06]

### Added
- Native WordPress Site Editor detection in `BlackBOX.php` matching `site-editor.php`, `wp-admin/site-editor.php`, `gutenberg-edit-site`, and canvas editing (`canvas=edit`).
- Automatic activation of master kill toggle (`BLACKBOX_BEDROCK_DISABLE = true`) when navigating within the native WordPress Site Editor.
- Secondary defense-in-depth site editor and `BLACKBOX_BEDROCK_DISABLE` guards across `Theme_Styler`, `Editor_Support`, `Menu_Manager`, `Core`, `PublicFace`, and `Error` modules.
- Dark modal styling solution for WordPress core Theme Details dialog (`.theme-overlay`) in `modal-overrides.css`, featuring glassmorphism, blurred backdrop overlay, gold/accent CTA buttons, and responsive layouts.
- Exclusion of `.theme-overlay`, `.theme-backdrop`, and `.theme-wrap` from the universal background stripper in `wp-admin.css`.

### Fixed
- Enforced complete `display: none !important`, `pointer-events: none !important`, and `visibility: hidden !important` on `.theme-overlay` when inactive, preventing the overlay from capturing clicks across WP Admin pages when no modal is open.
- Restored hard blur glass effect to WordPress Settings Connectors list cards (`.connectors-page .components-item`) and excluded them from universal background stripping in `assets/css/wp-admin.css`.
