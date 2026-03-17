# Changelog

All notable changes to AWDev Plugins Updater are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [0.1.0] — 2026-03-17

### Fixed
- `awdev_fetch_api_data()`: API responses with an empty or `null` body are now explicitly treated as failures and cached as `false` — previously `json_decode()` returned `null` without triggering the JSON error check, causing a `null` value to be stored in the transient
- `saveToggle()` in `settings.js`: missing `.then()`/`.catch()` chain meant save failures were silently ignored; the toggle is now visually reverted when the AJAX request fails
- Re-check button: version cell now resets to `…` *before* the fetch and shows `?` on error instead of staying stuck on `…` indefinitely
- `compareVersions()`: replaced `Number()` with `parseInt()` so pre-release suffixes like `-beta` are safely ignored instead of producing `NaN` and falsely indicating an update

### Added
- `register_activation_hook()` now calls `awdev_activate()` to set default options on first activation
- `awdev_sync_auto_update_defaults()`: new function that adds missing `awdev_auto_updates` entries for built-in plugins without overwriting existing ones; hooked to `admin_init` so newly added built-in plugins are picked up on existing installs without requiring a deactivate/activate cycle
- `escHtml()` in `settings.js` now also escapes apostrophes (`'` → `&#039;`) for full attribute and text context safety

### Changed
- `awdev_activate()` delegates defaults setup to `awdev_sync_auto_update_defaults()` to avoid duplication
- `AWDev_Updater::get_remote_data()` delegates all HTTP/cache logic to the shared `awdev_fetch_api_data()` helper — no more duplicated transient/HTTP code
- Removed unused jQuery dependency from `wp_enqueue_script()` — `settings.js` uses only vanilla JS
- JS Unicode escapes (`\u2013`, `\u2014`) replaced with literal UTF-8 characters throughout PHP files

### Also in this release (carried over from 0.0.9)
- `json_last_error()` validation after every `json_decode()` call — invalid API responses are treated as failures and logged via `error_log()`
- `error_log()` output when `WP_Filesystem::move()` fails during folder rename
- Days display in "last checked" time — intervals longer than 24 h show e.g. "3 days ago"
- Author field in "View version details" popup reads from API response; falls back to hard-coded default
- Standalone `uninstall.php` — all `awdev_*` options and transients removed on plugin deletion
- Built-in plugin registry centralised into `awdev_built_in_plugins()`
- GitHub Actions workflow `generate-l10n.yml` — auto-compiles `.po` → `.l10n.php` via WP-CLI
- Replaced `awdev_force_option()` pattern with `update_option()`
- Removed `register_uninstall_hook()` and inline `awdev_uninstall()` in favour of `uninstall.php`

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
