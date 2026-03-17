# AWDev Plugins Updater

<div align="center">

![Version](https://img.shields.io/badge/version-0.0.9-blue?style=flat-square)
![WordPress](https://img.shields.io/badge/WordPress-6.3%2B-21759b?style=flat-square&logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-GPLv2-green?style=flat-square)
![Tested up to](https://img.shields.io/badge/Tested%20up%20to-WP%206.9-21759b?style=flat-square)

**Self-hosted update manager for [AlexanderWagnerDev](https://alexanderwagnerdev.com) plugins.**  
No WordPress.org required — every release ships on your own schedule.

[Installation](#installation) · [Features](#features) · [Managing Plugins](#managing-plugins) · [Changelog](#changelog) · [Deutsch](README-de.md)

</div>

---

## Overview

AWDev Plugins Updater replaces the WordPress.org update channel for AlexanderWagnerDev plugins with a self-hosted update server. It hooks directly into the native WordPress update system — updates appear in the standard **Plugins** screen just like any other plugin update. No third-party libraries are used.

## Features

| Feature | Description |
|---|---|
| 🔄 Native WP integration | Updates appear in the standard Plugins screen |
| ⚙️ Settings page | *Settings → AWDev Plugins Updater* |
| 🌙 DarkAdmin built-in | Auto-registered when installed, full dark mode support |
| 🛡️ Per-plugin auto-update toggle | Saves instantly on click — no Save button needed |
| 🌍 Global master toggle | Applies state to all per-plugin toggles at once |
| 🔄 Manual re-check button | Clears transient and fetches latest version immediately |
| ⬆️ One-click Update button | Appears automatically when a newer version is available |
| ➕ Add/remove plugins | Via the Settings UI — no code changes needed |
| ⏱️ Configurable cache interval | 1h–168h, default 6h |
| 🗑️ Clean uninstall | All options and transients removed from DB on deletion |
| 🌎 Translations | `de_DE`, `de_AT`, `en_US` |

## Installation

1. Download the latest `awdev-plugins-updater.zip` from [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases).
2. In WordPress go to **Plugins → Add New → Upload Plugin** and select the ZIP.
3. Activate the plugin.
4. Open **Settings → AWDev Plugins Updater** — DarkAdmin is already registered automatically.

## Managing Plugins

Open **Settings → AWDev Plugins Updater**:

- **Built-in plugins** (AWDev Plugins Updater, DarkAdmin) are always registered automatically
- Use the **toggle** in the Auto-Update column to enable or disable background updates per plugin — saves instantly via AJAX
- The **global toggle** in the Auto-Update Settings card applies to all plugins at once
- Use the **🔄 re-check button** to force a fresh API fetch for a single plugin
- The **Update button** appears automatically when a newer version is available on the server
- Use **Flush Update Cache** to force an immediate full re-check across all plugins

## Changelog

### 0.0.9 — 2026-03-17

- **Added** `json_last_error()` validation after every `json_decode()` call — invalid API responses are treated as failures and logged via `error_log()`
- **Added** `error_log()` output when `WP_Filesystem::move()` fails during folder rename, making update failures diagnosable
- **Added** Days display in "last checked" time — intervals > 24 h now show e.g. *"3 days ago"* instead of *"72 hours ago"*
- **Added** Author field in "View version details" popup reads from API response when available; falls back to default
- **Added** Standalone [`uninstall.php`](uninstall.php) — all `awdev_*` options and transients removed from DB on plugin deletion; loaded by WordPress directly without bootstrapping the full plugin
- **Added** Built-in plugin registry centralised into `awdev_built_in_plugins()` — adding a new plugin requires only one entry
- **Changed** Replaced `awdev_force_option()` `DELETE` + `add_option()` pattern with `update_option()` to eliminate race condition risk

### 0.0.8

- **Fixed** Auto-update filter returned `null` instead of `true` for AWDev plugins, causing WP background updates to be silently skipped
- **Fixed** Plugin folder rename silently failed when target directory already existed after ZIP extraction

### 0.0.7

- **Changed** Improved DarkAdmin compatibility on the settings page
- **Changed** Removed colour-related `!important` declarations from base selectors so DarkAdmin styling can cascade properly
- **Changed** Kept DarkAdmin-specific override rules in place for reliable dark mode rendering

### 0.0.6

- **Fixed** Language file `msgid` strings now exactly match the corresponding `__()` calls in `settings.php`
- **Fixed** Corrected two mismatched strings: `'Configure how often...'` and `'Update data is cached for...'`
- **Fixed** Updated `Project-Id-Version` in all `.po` and `.pot` files to `0.0.6`

### 0.0.5

- **Fixed** Plugin folder rename failing on bulk updates (`update-core.php`) and WP auto-updates where `hook_extra['plugin']` is not populated
- **Added** Fallback matching by extracted source folder name (plugin slug + GitHub repo name from `download_url`)
- **Changed** Extracted rename logic into private `rename_source()` method to avoid duplication

### 0.0.4

- **Changed** Replaced `wp_redirect()` with `wp_safe_redirect()` throughout
- **Changed** Added `wp_unslash()` and `absint()` for all POST input sanitisation
- **Changed** Added `translators` comments for all `_n()` and `printf()` i18n calls
- **Changed** Replaced direct `rename()` with `WP_Filesystem::move()` for folder fix after ZIP extraction
- **Changed** Added `phpcs:ignore` annotations for intentional direct DB queries

### 0.0.3

- **Fixed** Illegal nested `<form>` elements causing "Save Settings" to submit the wrong action
- **Added** Per-plugin auto-update toggles now save instantly via AJAX — no Save button needed
- **Added** Global auto-update toggle now instantly mirrors its state to all per-plugin toggles
- **Changed** Debug output removed from settings saved notice
- **Changed** "Save Settings" button moved into the Auto-Update Settings card

### 0.0.2

- **Added** Per-plugin auto-update toggle
- **Added** Manual re-check button per plugin
- **Added** One-click Update button when newer remote version is detected
- **Fixed** Local installed version detection (folder-name fallback)
- **Fixed** Transient key mismatch causing version display to show dashes
- **Fixed** Dark Mode styling compatibility with DarkAdmin
- **Fixed** Remote version now actively fetched on settings page load if no transient exists

### 0.0.1

- Initial release
- Native WordPress update hook integration
- Settings page with managed plugin table and cache flush
- Built-in DarkAdmin support
- Translations: `de_DE`, `de_AT`, `en_US`

## License

Distributed under the **GPLv2 or later** license — see [LICENSE](LICENSE) for details.

---

<div align="center">
Made by <a href="https://alexanderwagnerdev.com">AlexanderWagnerDev</a> · <a href="https://github.com/AlexanderWagnerDev/wp-plugins-updater">GitHub</a>
</div>
