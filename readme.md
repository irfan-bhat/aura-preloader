# Aura Preloader

A professional, lightweight WordPress plugin that displays a customizable full-screen loading overlay with backdrop blur effect. Enhance user experience with a polished preloader featuring your logo, animated spinner ring, and optional progress bar.

## 📋 Plugin Information

| Property | Value |
|----------|-------|
| **Version** | 1.2.7 |
| **Requires WordPress** | 5.5 or higher |
| **Tested up to** | 6.5 |
| **Requires PHP** | 7.4 or higher |
| **License** | GPLv2 or later |
| **Text Domain** | aura-preloader |
| **Repository** | [GitHub](https://github.com/irfanbhat/aura-preloader) |
| **Author** | [Irfan Bhat](https://irfanbhat.com) |

## 🎯 Overview

Aura Preloader creates a sophisticated full-screen loading screen using modern CSS `backdrop-filter` blur and customizable tint effects. The semi-transparent overlay displays your branding while showing blurred site content beneath, providing users with immediate visual feedback that the page is loading.

## ✨ Core Features

### Customization
- 🖼️ Upload a custom logo using the WordPress media library
- 📐 Adjust logo width from 20px to 300px
- 🎨 Choose overlay tint color and opacity (0-100%)
- 🌀 Control blur strength from 0px to 40px
- 🎭 Set custom accent color for spinner and progress bar

### Visual Elements
- ⭕ Animated spinner ring (toggle on/off)
- 📊 Optional progress bar (toggle on/off)
- 📱 Mobile device detection with enable/disable toggle
- ⚡ Smooth fade-out transitions with configurable duration

### Performance & Compatibility
- Zero external dependencies — pure CSS and vanilla JavaScript
- Lightweight implementation (~10KB total)
- No jQuery required
- Compatible with all modern browsers
- Minimal performance impact on page load

## 🚀 Installation

### Step 1: Upload Plugin
1. Download the plugin from [GitHub Releases](https://github.com/irfanbhat/aura-preloader/releases)
2. Extract the `aura-preloader` folder
3. Upload to `/wp-content/plugins/`
4. Activate through **Plugins** menu in WordPress admin

### Step 2: Activate Theme Support
If your theme does not already include the `wp_body_open()` hook, add it:

Edit your theme's `header.php` and add this right after the opening `<body>` tag:

```php
<?php wp_body_open(); ?>
```

### Step 3: Configure Settings
1. Go to **Settings › Aura Preloader**
2. Upload your logo and adjust dimensions
3. Customize colors, opacity, and blur effects
4. Toggle elements and adjust timing
5. Click **Save Changes**

## 🔄 Automatic Updates

Starting with version 1.2.5, Aura Preloader includes built-in GitHub release integration. The plugin automatically:

- ✅ Checks for new releases every 12 hours
- 📬 Displays update notifications on the Plugins page
- 🔐 Validates version upgrades for data integrity
- ⬇️ Enables one-click automatic updates from GitHub
- 📦 Supports release assets and version validation

**No manual downloading required** — updates work just like WordPress.org plugins.

## ⚙️ Configuration Options

| Setting | Range | Default | Purpose |
|---------|-------|---------|---------|
| Overlay Color | Hex color | #ffffff | Background tint color |
| Overlay Opacity | 0-100% | 20% | Transparency of overlay |
| Blur Strength | 0-40px | 12px | Backdrop blur effect intensity |
| Accent Color | Hex color | #DC501E | Color for spinner/progress bar |
| Logo Width | 20-300px | 64px | Display size of logo image |
| Fade Duration | 0-3000ms | 500ms | Fade-out animation speed |
| Min Display | 0-5000ms | 800ms | Minimum time preloader shows |
| Show Ring | Boolean | ✓ Enabled | Display spinner animation |
| Show Bar | Boolean | ✓ Enabled | Display progress bar |
| Mobile Support | Boolean | ✓ Enabled | Show preloader on mobile |

## ❓ FAQ

### Q: My theme doesn't support `wp_body_open()`. What can I do?

**A:** Manually add `<?php wp_body_open(); ?>` to your theme's `header.php` immediately after the opening `<body>` tag. This hook is required for the preloader to render.

### Q: Can I disable the preloader on mobile devices?

**A:** Yes. In **Settings › Aura Preloader**, toggle the "Show on mobile devices" option off to disable it for mobile visitors.

### Q: How do I customize the overlay and blur effect?

**A:** Use the admin settings panel to:
- Select overlay tint color
- Adjust opacity slider (0-100%)
- Set blur strength (0-40px)

### Q: How often does the plugin check for updates?

**A:** The plugin checks GitHub for new releases every 12 hours. You can manually force a check by clearing the transient in the database or deactivating/reactivating the plugin.

### Q: Is the preloader visible to search engines?

**A:** No. The preloader is hidden via inline JavaScript and CSS, and only displays to end-users, not bot crawlers.

### Q: Can I customize the preloader appearance further?

**A:** Yes. All CSS variables and animations are defined in `css/preloader.css`. Advanced users can override styles using custom CSS in their theme.

## 📊 Changelog

### Version 1.2.7 (Latest)

**Rebranding & Preset Enhancements:**
- 🎨 Rebranded plugin to **Aura Preloader** (`aura-preloader`)
- 🚀 Enhanced preset integration and migration support

### Version 1.2.5

**New Features:**
- ✨ Automatic GitHub release integration with update checker
- 🔄 One-click plugin updates without WordPress.org submission
- 🔐 Version upgrade validation in CI/CD pipeline
- 📦 Automated ZIP release generation

**Improvements:**
- Enhanced release workflow with semantic versioning checks
- Better error handling for remote version checks
- Improved compatibility with GitHub API rate limiting

### Version 1.2.4

- Refined performance optimizations
- Updated JavaScript bundle

### Version 1.2.0

- Added frosted-glass backdrop blur effect
- Blur strength slider (0–40px)
- Overlay opacity slider (0-100%)
- Overlay tint color picker
- Accent color support for UI elements
- Improved admin interface with live preview

### Version 1.0.0

- Initial release
- Core preloader functionality
- Basic customization options

## 📜 License

This plugin is licensed under the **GNU General Public License v2.0 or later** (GPLv2 or later).

```text
Aura Preloader - WordPress Plugin
Copyright (C) 2024-2026 Irfan Bhat

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

See [LICENSE](./LICENSE) file for complete terms.

## 🐛 Support & Issues

Found a bug or have a feature request? Please open an issue on [GitHub Issues](https://github.com/irfanbhat/aura-preloader/issues).

## 👨‍💻 Author

**Irfan Bhat**
- Website: [irfanbhat.com](https://irfanbhat.com)
- GitHub: [@irfanbhat](https://github.com/irfanbhat)

## 🤝 Contributing

Contributions are welcome! For bug fixes and improvements:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/improvement`)
3. Commit your changes (`git commit -m 'Add improvement'`)
4. Push to the branch (`git push origin feature/improvement`)
5. Open a Pull Request

## 📝 Notes

- This plugin uses the `wp_body_open()` hook, introduced in WordPress 5.2. For older versions, manual theme modification is required.
- Requires PHP 7.4+ for proper functionality.
- Best viewed in modern browsers that support CSS `backdrop-filter`.

