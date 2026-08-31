=== Vizmal Preloader ===
Contributors: irfanbhat
Tags: preloader, loading screen, spinner, progress bar, backdrop blur
Requires at least: 5.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.3.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A customisable full-screen backdrop-blur preloader with your logo, spinner, and progress bar.

== Description ==

Vizmal Preloader creates a sophisticated full-screen loading screen using modern CSS `backdrop-filter` blur and customizable tint effects. The semi-transparent overlay displays your branding while showing blurred site content beneath, providing users with immediate visual feedback that the page is loading.

== Features ==

* Upload a custom logo using the WordPress media library
* Adjust logo width from 20px to 300px
* Choose overlay tint color and opacity (0-100%)
* Control blur strength from 0px to 40px
* Set custom accent color for spinner and progress bar
* Animated spinner ring and progress bar presets
* Mobile device detection toggle
* Zero external dependencies — pure CSS and vanilla JavaScript

== Installation ==

1. Upload the `vizmal-preloader` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress admin.
3. Go to **Settings › Vizmal Preloader** to configure options.

== Frequently Asked Questions ==

= My theme doesn't support wp_body_open(). What can I do? =
Manually add `<?php wp_body_open(); ?>` to your theme's `header.php` immediately after the opening `<body>` tag.

= Can I disable the preloader on mobile devices? =
Yes. In **Settings › Vizmal Preloader**, toggle the "Show on mobile devices" option off.

== Changelog ==

= 1.3.0 =
* Rebranded plugin to Vizmal Preloader.
* Added preset styles and progress bar configurations.
* Updated compatibility for latest WordPress releases.

= 1.2.5 =
* Added initial release integrations.

= 1.0.0 =
* Initial release.
