# Changelog

All notable changes to the BlackBOX Bedrock submodule will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [2026-09-06]

### Added
- Native WordPress Site Editor detection in `BlackBOX.php` matching `site-editor.php`, `wp-admin/site-editor.php`, `gutenberg-edit-site`, and canvas editing (`canvas=edit`).
- Automatic activation of master kill toggle (`BLACKBOX_BEDROCK_DISABLE = true`) when navigating within the native WordPress Site Editor.
- Secondary defense-in-depth site editor and `BLACKBOX_BEDROCK_DISABLE` guards across `Theme_Styler`, `Editor_Support`, `Menu_Manager`, `Core`, `PublicFace`, and `Error` modules.
- Dark modal styling solution for WordPress core Theme Details dialog (`.theme-overlay`) in `modal-overrides.css`, featuring glassmorphism, blurred backdrop overlay, gold/accent CTA buttons, and responsive layouts.
- Exclusion of `.theme-overlay`, `.theme-backdrop`, and `.theme-wrap` from the universal background stripper in `wp-admin.css`.

### Fixed
- Enforced complete `display: none !important`, `pointer-events: none !important`, and `visibility: hidden !important` on `.theme-overlay` when inactive, preventing the overlay from capturing clicks across WP Admin pages when no modal is open.
