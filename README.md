# AWDev Plugins Updater

![License: GPLv2](https://img.shields.io/badge/License-GPLv2-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.3%2B-21759b)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)

Keeps [AlexanderWagnerDev](https://alexanderwagnerdev.com) plugins up to date — without WordPress.org. Updates are served from a self-hosted server, so every release ships on your own schedule.

## Features

- ✅ Native WordPress update integration — updates appear in the standard Plugins screen
- ✅ Settings page under *Settings → AWDev Plugins Updater*
- ✅ DarkAdmin built-in — auto-registered when installed
- ✅ Per-plugin **Auto-Update toggle** — saves instantly on click, no Save button needed
- ✅ Global **Auto-Update master toggle** — instantly applies state to all per-plugin toggles
- ✅ **Manual re-check button** per plugin — clears transient and fetches latest version immediately
- ✅ **One-click Update button** — appears automatically when a newer remote version is available
- ✅ Add/remove additional plugins via the Settings UI — no code changes needed
- ✅ Configurable update cache interval (1h–168h, default 6h)
- ✅ Manual full cache flush button
- ✅ "View version details" popup in the WP update screen
- ✅ Automatic plugin folder name fix after ZIP extraction (including bulk updates and random-suffix folders)
- ✅ Full Dark Mode support via DarkAdmin — settings page adapts automatically
- ✅ Translations: `de_DE`, `de_AT`, `en_US`

## Installation

1. Download the latest `awdev-plugins-updater.zip` from [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases).
2. In WordPress go to *Plugins → Add New → Upload Plugin* and select the ZIP.
3. Activate the plugin.
4. Open *Settings → AWDev Plugins Updater* — DarkAdmin is already registered automatically.

## Managing Plugins

Open *Settings → AWDev Plugins Updater*:

- **Built-in plugins** (AWDev Plugins Updater, DarkAdmin) are always registered automatically
- Use the **toggle** in the Auto-Update column to enable or disable automatic WP updates per plugin — saves instantly
- The **global toggle** in the Auto-Update Settings card applies to all plugins at once
- Use the **🔄 re-check button** to force a fresh API fetch for a single plugin
- The **Update button** appears automatically when a newer version is available on the server

## Changelog

### 0.0.8
- Fixed auto-update filter returning `null` for AWDev plugins instead of `true`, causing WP background updates to be silently skipped
- Fixed plugin folder rename silently failing when target directory already exists after ZIP extraction (`WP_Filesystem::move()` now preceded by `delete()` of existing target)

### 0.0.7
- Improved DarkAdmin compatibility on the AWDev Plugins Updater settings page
- Removed color-related `!important` declarations from base selectors so DarkAdmin styling can cascade properly
- Kept DarkAdmin-specific override rules in place for reliable dark mode rendering

### 0.0.6
- Fixed language file `msgid` strings to exactly match the corresponding `__()` calls in `settings.php`
- Corrected two mismatched strings: `'Configure how often...'` and `'Update data is cached for...'`
- Updated `Project-Id-Version` in all `.po` and `.pot` files to `0.0.6`

### 0.0.5
- Fixed plugin folder rename failing on bulk updates (`update-core.php`) and WP auto-updates where `hook_extra['plugin']` is not populated
- Added fallback matching by extracted source folder name: matches against plugin slug and GitHub repo name derived from `download_url`
- Extracted rename logic into private `rename_source()` method to avoid duplication

### 0.0.4
- Replaced `wp_redirect()` with `wp_safe_redirect()` throughout
- Added `wp_unslash()` and `absint()` for all POST input sanitization
- Added `translators` comments for all `_n()` and `printf()` i18n calls
- Replaced direct `rename()` with `WP_Filesystem::move()` for folder fix after ZIP extraction
- Added `phpcs:ignore` annotations for intentional direct DB queries
- Shortened `readme.txt` short description to comply with 150-char limit

### 0.0.3
- Fixed illegal nested `<form>` elements causing "Save Settings" to submit the wrong action
- Per-plugin auto-update toggles now save instantly via AJAX — no Save button needed
- Global auto-update toggle now instantly mirrors its state to all per-plugin toggles and saves via AJAX
- Debug output removed from settings saved notice
- "Save Settings" button moved into the Auto-Update Settings card

### 0.0.2
- Added per-plugin auto-update toggle (stored in `awdev_auto_updates` option)
- Added manual re-check button per plugin
- Added one-click Update button when newer remote version is detected
- Fixed local installed version detection (folder-name fallback)
- Fixed transient key mismatch for DarkAdmin version display
- Fixed Dark Mode styling compatibility with DarkAdmin
- Remote version is now actively fetched on settings page load if no transient exists

### 0.0.1
- Initial release
- Native WordPress update hook integration
- Settings page with configurable API URL, managed plugin table and cache flush
- Built-in DarkAdmin support
- Translations: `de_DE`, `de_AT`, `en_US`

## License

GPLv2 or later — see [LICENSE](LICENSE)

---

## Deutsch

Hält [AlexanderWagnerDev](https://alexanderwagnerdev.com) Plugins aktuell — ohne WordPress.org.

### Changelog

#### 0.0.8
- Auto-Update-Filter gab `null` statt `true` für AWDev-Plugins zurück — WP-Hintergrund-Updates wurden dadurch lautlos übersprungen (behoben)
- Ordnerumbenennung nach ZIP-Extraktion schlug lautlos fehl wenn Zielordner bereits existierte — `WP_Filesystem::move()` wird nun von `delete()` des Zielordners vorangestellt (behoben)

#### 0.0.7
- DarkAdmin-Kompatibilität auf der Einstellungsseite des AWDev Plugins Updater verbessert
- Farbbezogene `!important`-Deklarationen aus Basis-Selektoren entfernt, damit DarkAdmin-Styles korrekt durchgreifen
- DarkAdmin-spezifische Override-Regeln für stabiles Dark-Mode-Rendering beibehalten

#### 0.0.6
- Sprachdatei-`msgid`-Strings an die exakten `__()`-Aufrufe in `settings.php` angepasst
- Zwei nicht übereinstimmende Strings korrigiert
- `Project-Id-Version` in allen `.po`- und `.pot`-Dateien auf `0.0.6` aktualisiert

#### 0.0.5
- Fehler bei Ordnerumbenennung bei Bulk-Updates (`update-core.php`) und WP Auto-Updates behoben
- Fallback-Matching über extrahierten Quellordnernamen hinzugefügt
- Rename-Logik in private `rename_source()`-Methode ausgelagert

#### 0.0.4
- `wp_redirect()` durch `wp_safe_redirect()` ersetzt
- `wp_unslash()` und `absint()` für POST-Input hinzugefügt
- `translators`-Kommentare hinzugefügt
- `rename()` durch `WP_Filesystem::move()` ersetzt

#### 0.0.3
- Verschachtelte `<form>`-Elemente behoben
- Per-Plugin-Toggles speichern sofort via AJAX
- Globaler Toggle spiegelt Zustand sofort
- Debug-Ausgaben entfernt

#### 0.0.2
- Auto-Update-Toggle pro Plugin
- Re-Check-Button pro Plugin
- Ein-Klick-Update-Button
- Diverse Fixes

#### 0.0.1
- Erste Veröffentlichung

### Lizenz

GPLv2 oder höher — siehe [LICENSE](LICENSE)
