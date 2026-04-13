# 🔳 BlackBOX Bedrock

> **"The Systems Interface for the Digital Sovereign."**

BlackBOX Bedrock is the foundational MU-plugin framework for the **w⁴ Protocol** and **YouMeOS** ecosystem. It provides a sleek, high-performance administrative interface, glassmorphism aesthetics, and native environment overrides for WordPress.

## ✨ Features

- 🎨 **Glassmorphism UI**: Native overrides for the WordPress Admin, Gutenberg, and Login screens.
- 🔳 **SUI (Systems User Interface)**: A refined, high-contrast command center design.
- 🌊 **Liquid Smoke Animation**: Interactive, procedurally generated background visuals for the admin deck.
- 📦 **Composer Ready**: Designed to be installed via Composer as a `wordpress-muplugin`.

## 🚀 Installation

### Using Composer (Recommended)

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/hallofthegods/blackbox-bedrock"
        }
    ],
    "require": {
        "hallofthegods/blackbox-bedrock": "dev-master"
    }
}
```

### Manual Installation

1. Copy the `BlackBOX` folder into `wp-content/mu-plugins/`.
2. Create a loader file in `wp-content/mu-plugins/load.php`:
   ```php
   <?php
   require_once __DIR__ . '/BlackBOX/BlackBOX.php';
   new BlackBOX_MU_Core();
   ```

## 🛠️ Architecture

- `BlackBOX.php`: Core logic and hook registration.
- `assets/css/`: System styles including base, sui, and environment-specific overrides.
- `composer.json`: Package metadata and installation instructions.

---

*Engineered by [Hall of the Gods](http://www.hallofthegods.com/)*.
