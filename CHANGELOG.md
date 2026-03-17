# Changelog

All notable changes to AWDev Plugins Updater are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [0.0.9] — 2026-03-17

### Added
- `json_last_error()` validation after every `json_decode()` call — invalid API responses are now treated as failures and logged via `error_log()`
- `error_log()` output when `WP_Filesystem::move()` fails during folder rename, making update failures diagnosable
- Days display in "last checked" time — cache intervals longer than 24 h now show e.g. "3 days ago" instead of "72 hours ago"
- Author field in "View version details" popup now reads from API response when provided; falls back to hard-coded default
- Standalone `uninstall.php` — all `awdev_*` options and transients are removed from the database when the plugin is deleted; loaded by WordPress directly without bootstrapping the full plugin
- Built-in plugin registry centralised into `awdev_built_in_plugins()` — adding a new plugin requires only one entry

### Changed
- Replaced `awdev_force_option()` `DELETE` + `add_option()` pattern with `update_option()` to eliminate race condition risk
- Removed `register_uninstall_hook()` and inline `awdev_uninstall()` from main plugin file in favour of `uninstall.php`

---

## [0.0.8] — 2025-12-01

### Fixed
- Auto-update filter returned `null` instead of `true` for AWDev plugins, causing WordPress background updates to be silently skipped
- Plugin folder rename silently failed when the target directory already existed after ZIP extraction

---

## [0.0.7]

### Changed
- Improved DarkAdmin compatibility on the settings page
- Removed colour-related `!important` declarations from base selectors so DarkAdmin styling can cascade properly
- Kept DarkAdmin-specific override rules in place for reliable dark mode rendering

---

## [0.0.6]

### Fixed
- Language file `msgid` strings now exactly match the corresponding `__()` calls in `settings.php`
- Corrected two mismatched strings: `Configure how often…` and `Update data is cached for…`
- Updated `Project-Id-Version` in all `.po` and `.pot` files to `0.0.6`

---

## [0.0.5]

### Fixed
- Plugin folder rename failed on bulk updates (`update-core.php`) and WP auto-updates where `hook_extra['plugin']` is not populated
- Added fallback matching by extracted source folder name (matches against plugin slug and GitHub repo name derived from download URL)

### Changed
- Extracted rename logic into private `rename_source()` method to avoid duplication

---

## [0.0.4]

### Changed
- Replaced `wp_redirect()` with `wp_safe_redirect()` throughout
- Added `wp_unslash()` and `absint()` for all POST input sanitisation
- Added `translators` comments for all `_n()` and `printf()` i18n calls
- Replaced direct `rename()` with `WP_Filesystem::move()` for folder fix after ZIP extraction
- Added `phpcs:ignore` for intentional direct DB queries

---

## [0.0.3]

### Fixed
- Illegal nested form elements causing "Save Settings" to submit the wrong action

### Added
- Per-plugin auto-update toggles now save instantly via AJAX — no Save button needed
- Global auto-update toggle now instantly mirrors its state to all per-plugin toggles and saves via AJAX

### Changed
- Debug output removed from settings saved notice
- "Save Settings" button moved into the Auto-Update Settings card

---

## [0.0.2]

### Added
- Per-plugin auto-update toggle
- Manual re-check button per plugin
- One-click Update button when newer remote version is detected

### Fixed
- Local installed version detection (folder-name fallback)
- Transient key mismatch causing version display to show dashes
- Dark Mode styling compatibility with DarkAdmin
- Remote version is now actively fetched on settings page load if no transient exists

---

## [0.0.1]

### Added
- Initial release
- Native WordPress update hook integration
- Settings page with managed plugin table and cache flush
- Built-in DarkAdmin support
- Translations: de\_DE, de\_AT, en\_US
