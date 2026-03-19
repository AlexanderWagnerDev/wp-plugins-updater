=== AWDev Plugins Updater ===
Contributors: alexanderwagnerdev
Tags: updater, awdev, plugin update, wordpress update
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Updater for AlexanderWagnerDev plugins. No WordPress.org required.

== Description ==
AWDev Plugins Updater replaces the WordPress.org update channel for AlexanderWagnerDev plugins with a direct connection to the AWDev update server. This gives full control over versioning, distribution and release timing — completely independent of the WordPress.org review queue.

The updater hooks directly into the native WordPress update system, so updates appear in the standard Plugins screen just like any other plugin update. No third-party update libraries are used.

Features:
* Native WordPress update integration (no third-party libraries)
* Settings page under Settings > AWDev Plugins Updater
* Built-in support for DarkAdmin – Dark Mode for Adminpanel (auto-registered)
* Per-plugin auto-update toggle — saves instantly on click, no Save button needed
* Global auto-update master toggle — instantly applies state to all per-plugin toggles
* Manual re-check button per plugin — clears transient and fetches latest version immediately
* One-click Update button — appears automatically when a newer remote version is available
* Configurable update cache interval (1h–168h, default 6h)
* Manual full cache flush button
* "View version details" popup support in WP update screen
* Automatic plugin folder name fix after ZIP extraction (including bulk updates and random-suffix folders)
* Full Dark Mode support — settings page adapts automatically via DarkAdmin CSS variables
* Translations: de_DE, de_AT, en_US
* Clean uninstall — all plugin options are removed from the database on deletion

== Installation ==
1. Download the latest awdev-plugin-updater.zip from the GitHub Releases page.
2. Upload via Plugins > Add New > Upload Plugin or extract to /wp-content/plugins/awdev-plugin-updater/.
3. Activate the plugin through the Plugins screen in WordPress.
4. Go to Settings > AWDev Plugins Updater to verify managed plugins.

== Frequently Asked Questions ==
= Do I need to configure anything? =
No. DarkAdmin is registered automatically when installed. The default server URL is pre-configured.

= How does the auto-update toggle work? =
Each plugin has an Auto-Update toggle on the Settings page. Clicking it saves the state instantly via AJAX — no Save button needed. When enabled, WordPress will automatically install updates during the regular background update cycle.

= How do I manually trigger an update check? =
Click the re-check button (circular arrow icon) next to any plugin on the Settings page. This clears the cached version data and fetches the latest version from the server immediately.

= How often does WordPress check for updates? =
WordPress checks approximately every 12 hours by default. The updater caches API responses for the configured interval (default 6h). Use the "Flush Update Cache" button to force an immediate full re-check.

= Does this replace WordPress.org updates? =
Only for AlexanderWagnerDev plugins registered in this updater. All other plugins continue to update normally via WordPress.org.

= Is this compatible with Dark Mode? =
Yes. The settings page fully supports DarkAdmin and adapts to dark mode automatically.

== Changelog ==
= 0.1.2 =
* Removed error logging calls
* Removed dead add-plugin JS code (no UI counterpart existed)

= 0.1.1 =
* Fixed fix_folder_name(): crash when ZIP extracts without a subfolder (flat structure) — rename() was called with target path inside the source directory producing Invalid argument; source/remote-source paths are now normalised and compared, rename redirected to sibling directory
* Fixed plugins_loaded hook priority raised to 20 for reliable init order
* Changed rename_source() now receives fully resolved target path directly
* Changed awdev_get_local_version() caches get_plugins() via wp_cache_get/set (group awdev_updater) — at most one filesystem scan per request

= 0.1.0 =
* Fixed awdev_fetch_api_data(): null/empty API body now explicitly treated as failure and cached as false
* Fixed saveToggle() in settings.js: missing error handling caused silent failures — toggle now reverts visually on error
* Fixed re-check button: version cell resets to ... before fetch and shows ? on error instead of staying stuck
* Fixed compareVersions(): Number() replaced with parseInt() to safely ignore pre-release suffixes like -beta
* Added awdev_sync_auto_update_defaults(): new built-in plugin defaults picked up without deactivate/activate cycle
* Added escHtml() in settings.js now escapes apostrophes for full attribute/text safety
* Changed: removed unused jQuery dependency from wp_enqueue_script()
* Also included from 0.0.9: json_last_error() validation, error_log() on rename failure, days display in last-checked, author field from API, uninstall.php, awdev_built_in_plugins(), generate-l10n.yml workflow, update_option() refactor

= 0.0.8 =
* Fixed auto-update filter returning null for AWDev plugins instead of true, causing WP background updates to be silently skipped
* Fixed plugin folder rename silently failing when target directory already exists after ZIP extraction

= 0.0.7 =
* Improved DarkAdmin compatibility on the AWDev Plugins Updater settings page
* Removed color-related !important declarations from base selectors so DarkAdmin styling can cascade properly
* Kept DarkAdmin-specific override rules in place for reliable dark mode rendering

= 0.0.6 =
* Fixed language file msgid strings to exactly match the corresponding __() calls in settings.php
* Corrected two mismatched strings: 'Configure how often...' and 'Update data is cached for...'
* Updated Project-Id-Version in all .po and .pot files to 0.0.6

= 0.0.5 =
* Fixed plugin folder rename failing on bulk updates (update-core.php) and WP auto-updates where hook_extra['plugin'] is not populated
* Added fallback matching by extracted source folder name
* Extracted rename logic into private rename_source() method to avoid duplication

= 0.0.4 =
* Replaced wp_redirect() with wp_safe_redirect() throughout.
* Added wp_unslash() and absint() for all POST input sanitization.
* Added translators comments for all _n() and printf() i18n calls.
* Replaced direct rename() with WP_Filesystem::move() for folder fix after ZIP extraction.
* Added phpcs:ignore for intentional direct DB queries.

= 0.0.3 =
* Fixed illegal nested form elements causing "Save Settings" to submit the wrong action.
* Per-plugin auto-update toggles now save instantly via AJAX — no Save button needed.
* Global auto-update toggle now instantly mirrors its state to all per-plugin toggles and saves via AJAX.
* Debug output removed from settings saved notice.
* "Save Settings" button moved into the Auto-Update Settings card.

= 0.0.2 =
* Added per-plugin auto-update toggle.
* Added manual re-check button per plugin.
* Added one-click Update button when newer remote version is detected.
* Fixed local installed version detection (folder-name fallback).
* Fixed transient key mismatch causing version display to show dashes.
* Fixed Dark Mode styling compatibility with DarkAdmin.
* Remote version is now actively fetched on settings page load if no transient exists.

= 0.0.1 =
* Initial release.
* Native WordPress update hook integration.
* Settings page with managed plugin table and cache flush.
* Built-in DarkAdmin support.
* Translations: de_DE, de_AT, en_US.

== Deutsch ==
Der AWDev Plugins Updater ersetzt den WordPress.org-Update-Kanal fuer AlexanderWagnerDev-Plugins durch eine direkte Verbindung zum AWDev Update-Server.

Funktionen:
* Native WordPress-Update-Integration (keine Drittanbieter-Bibliotheken)
* Einstellungsseite unter Einstellungen > AWDev Plugins Updater
* Integrierte Unterstuetzung fuer DarkAdmin (automatisch registriert)
* Auto-Update-Toggle pro Plugin — wird sofort beim Klick gespeichert
* Globaler Auto-Update-Hauptschalter — uebertraegt Zustand sofort auf alle Per-Plugin-Toggles
* Manueller Re-Check-Button pro Plugin
* Ein-Klick-Aktualisieren-Button bei verfuegbarer neuerer Version
* Konfigurierbares Update-Cache-Intervall (1h–168h, Standard 6h)
* Manueller Cache-Flush-Button
* Automatische Ordnernamens-Korrektur nach ZIP-Extraktion (inkl. Bulk-Updates und Zufalls-Suffix-Ordner)
* Dark-Mode-kompatible Einstellungsseite via DarkAdmin
* Uebersetzungen: de_DE, de_AT, en_US
* Saubere Deinstallation — alle Plugin-Optionen werden beim Loeschen aus der Datenbank entfernt

=== Installation ===
1. Neuste awdev-plugin-updater.zip von der GitHub-Releases-Seite herunterladen.
2. Ueber Plugins > Neu hinzufuegen > Plugin hochladen installieren.
3. Plugin aktivieren.
4. Einstellungen > AWDev Plugins Updater aufrufen.

=== Changelog ===
= 0.1.2 =
* Removed error logging calls
* Removed dead add-plugin JS code (no UI counterpart existed)

= 0.1.1 =
* fix_folder_name(): Absturz behoben wenn ZIP-Inhalt ohne Unterordner extrahiert wird (flache Struktur) — Umbenennung in Geschwisterverzeichnis umgeleitet
* plugins_loaded-Prioritaet auf 20 erhoeht
* rename_source() erhaelt jetzt den vollstaendig aufgeloesten Zielpfad direkt
* awdev_get_local_version() cached get_plugins() via wp_cache_get/set

= 0.1.0 =
* awdev_fetch_api_data(): null/leerer API-Body wird jetzt explizit als Fehler behandelt und als false gecacht
* saveToggle() in settings.js: fehlende Fehlerbehandlung behoben — Toggle wird bei Fehler visuell zurueckgesetzt
* Re-Check-Button: Versions-Zelle wird vor dem Fetch auf ... zurueckgesetzt und zeigt ? bei Fehler
* compareVersions(): Number() durch parseInt() ersetzt fuer sichere Behandlung von Pre-Release-Suffixen
* awdev_sync_auto_update_defaults() hinzugefuegt — neue Built-in-Defaults ohne Deaktivieren/Aktivieren
* escHtml() in settings.js escapt jetzt auch Apostrophe
* Ungenutzte jQuery-Abhaengigkeit aus wp_enqueue_script() entfernt
* Ebenfalls enthalten (von 0.0.9): json_last_error()-Validierung, error_log() bei Rename-Fehler, Tages-Anzeige, Autor-Feld aus API, uninstall.php, awdev_built_in_plugins(), generate-l10n.yml, update_option()-Refactoring

= 0.0.8 =
* Auto-Update-Filter gab null statt true fuer AWDev-Plugins zurueck (behoben)
* Ordnerumbenennung nach ZIP-Extraktion schlug lautlos fehl (behoben)

= 0.0.7 =
* DarkAdmin-Kompatibilitaet verbessert
* Farbbezogene !important-Deklarationen entfernt
* DarkAdmin-Override-Regeln beibehalten

= 0.0.6 =
* Sprachdatei-msgid-Strings angepasst
* Zwei nicht uebereinstimmende Strings korrigiert
* Project-Id-Version auf 0.0.6 aktualisiert

= 0.0.5 =
* Fehler bei Ordnerumbenennung bei Bulk-Updates behoben
* Fallback-Matching hinzugefuegt
* Rename-Logik in rename_source() ausgelagert

= 0.0.4 =
* wp_safe_redirect() verwendet
* wp_unslash() und absint() fuer POST-Input
* translators-Kommentare hinzugefuegt
* WP_Filesystem::move() statt rename()
* phpcs:ignore fuer direkte DB-Queries

= 0.0.3 =
* Verschachtelte Formular-Elemente behoben
* AJAX-Toggles hinzugefuegt
* Debug-Ausgaben entfernt

= 0.0.2 =
* Auto-Update-Toggle, Re-Check-Button, Aktualisieren-Button hinzugefuegt
* Versionserkennung, Transient-Key, Dark-Mode behoben

= 0.0.1 =
* Erste Veroeffentlichung
* Native WordPress-Update-Hook-Integration
* Einstellungsseite mit verwalteter Plugin-Tabelle und Cache-Flush
* Integrierte DarkAdmin-Unterstuetzung
* Uebersetzungen: de_DE, de_AT, en_US
