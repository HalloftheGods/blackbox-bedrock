# Changelog

All notable changes to the BlackBOX Bedrock submodule will be documented in this file.

## [2026-09-08]

### Added
- Created `Admin\Bar_Toggle` module (`admin/class-admin-bar-toggle.php`) and registered it in `Admin` (`admin/class-blackbox-admin.php`). Adds an interactive theme switch directly to `#wp-admin-bar-top-secondary` adjacent to `#wp-admin-bar-my-account`, enabling users to toggle between BlackBOX Bedrock and Classic WordPress admin themes with instant tactile feedback, authenticated AJAX synchronization (`wp_ajax_blackbox_toggle_theme`), and clean page reload.
- Comprehensive root design token takeover in `assets/css/sui.css` overriding `--wpmudev-*` and `--sui-*` CSS custom properties on `:root`, `html`, `body`, `.sui-wrap`, `.wpmudev-core-ui`, and `.wpmudev-ui-root`. Maps surface foundations, typography contrast, grays, and borders into the Starship glass aesthetic.
- Component surface rules in `assets/css/sui.css` for modern WPMU DEV Core UI and WP Defender cards, panels, lists, inputs, and toggles (`[class*="wpmudev-card"]`, `.wpmudev-setting-card`, `[class*="defender-ar-"]`, `.defender-schedule-scan-pill`, `.defender-pill-toggle-group`), preventing blinding white backgrounds across Defender alerts and reports.
- Added exclusions for `.wpmudev-core-ui`, `.wpmudev-ui-root`, `[class*="wpmudev-"]`, `[class*="defender-"]`, `.defender-schedule-scan-pill`, and `.defender-pill-toggle-group` to the Universal Background Stripper in `assets/css/wp-admin.css`.
- Added Admin Bar theme toggle layout rules in `assets/css/wp-admin.css` to exempt the toggle from mobile and compact icon hiding rules.

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
