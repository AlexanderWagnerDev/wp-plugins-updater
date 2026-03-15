=== AWDev Plugins Updater ===
Contributors: alexanderwagnerdev
Tags: updater, self-hosted, plugin update, update manager, dark mode
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.0.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Self-hosted update manager for AlexanderWagnerDev plugins. No WordPress.org required.

== Description ==
AWDev Plugins Updater replaces the WordPress.org update channel for AlexanderWagnerDev plugins with a self-hosted update server. This gives full control over versioning, distribution and release timing — completely independent of the WordPress.org review queue.

The updater hooks directly into the native WordPress update system, so updates appear in the standard Plugins screen just like any other plugin update. No third-party update libraries are used.

Features:
* Native WordPress update integration (no third-party libraries)
* Settings page under Settings > AWDev Plugins Updater
* Built-in support for DarkAdmin – Dark Mode for Adminpanel (auto-registered)
* Per-plugin auto-update toggle — saves instantly on click, no Save button needed
* Global auto-update master toggle — instantly applies state to all per-plugin toggles
* Manual re-check button per plugin — clears transient and fetches latest version immediately
* One-click Update button — appears automatically when a newer remote version is available
* Add additional plugins via the Settings UI — no code changes needed
* Configurable update cache interval (1h–168h, default 6h)
* Manual full cache flush button
* "View version details" popup support in WP update screen
* Automatic plugin folder name fix after ZIP extraction (including bulk updates and random-suffix folders)
* Full Dark Mode support — settings page adapts automatically via DarkAdmin CSS variables
* Translations: de_DE, de_AT, en_US

== Installation ==
1. Download the latest awdev-plugin-updater.zip from the GitHub Releases page.
2. Upload via Plugins > Add New > Upload Plugin or extract to /wp-content/plugins/awdev-plugin-updater/.
3. Activate the plugin through the Plugins screen in WordPress.
4. Go to Settings > AWDev Plugins Updater to verify managed plugins.

== Frequently Asked Questions ==
= Do I need to configure anything? =
No. DarkAdmin is registered automatically when installed. The default server URL is pre-configured.

= Can I add other plugins? =
Yes. Use the Settings page to add any additional AlexanderWagnerDev plugin by providing its basename (e.g. my-plugin/my-plugin.php) and API slug.

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
= 0.0.6 =
* Fixed language file msgid strings to exactly match the corresponding __() calls in settings.php
* Corrected two mismatched strings: 'Configure how often...' and 'Update data is cached for...'
* Updated Project-Id-Version in all .po and .pot files to 0.0.6

= 0.0.5 =
* Fixed plugin folder rename failing on bulk updates (update-core.php) and WP auto-updates where hook_extra['plugin'] is not populated
* Added fallback matching by extracted source folder name: matches against plugin slug and GitHub repo name derived from download URL
* Extracted rename logic into private rename_source() method to avoid duplication

= 0.0.4 =
* Replaced wp_redirect() with wp_safe_redirect() throughout.
* Added wp_unslash() and absint() for all POST input sanitization.
* Added translators comments for all _n() and printf() i18n calls.
* Replaced direct rename() with WP_Filesystem::move() for folder fix after ZIP extraction.
* Added phpcs:ignore for intentional direct DB queries.
* Shortened readme.txt short description to comply with 150-char limit.

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
Der AWDev Plugins Updater ersetzt den WordPress.org-Update-Kanal für AlexanderWagnerDev-Plugins durch einen selbst gehosteten Update-Server.

Funktionen:
* Native WordPress-Update-Integration (keine Drittanbieter-Bibliotheken)
* Einstellungsseite unter Einstellungen → AWDev Plugins Updater
* Integrierte Unterstützung für DarkAdmin (automatisch registriert)
* Auto-Update-Toggle pro Plugin — wird sofort beim Klick gespeichert
* Globaler Auto-Update-Hauptschalter — überträgt Zustand sofort auf alle Per-Plugin-Toggles
* Manueller Re-Check-Button pro Plugin
* Ein-Klick-Aktualisieren-Button bei verfügbarer neuerer Version
* Weitere Plugins über die Einstellungsseite hinzufügen
* Konfigurierbares Update-Cache-Intervall (1h–168h, Standard 6h)
* Manueller Cache-Flush-Button
* Automatische Ordnernamens-Korrektur nach ZIP-Extraktion (inkl. Bulk-Updates und Zufalls-Suffix-Ordner)
* Dark-Mode-kompatible Einstellungsseite via DarkAdmin
* Übersetzungen: de_DE, de_AT, en_US

=== Installation ===
1. Neuste awdev-plugin-updater.zip von der GitHub-Releases-Seite herunterladen.
2. Über Plugins → Neu hinzufügen → Plugin hochladen installieren.
3. Plugin aktivieren.
4. Einstellungen → AWDev Plugins Updater aufrufen.

=== Changelog ===
= 0.0.6 =
* Sprachdatei-msgid-Strings an die exakten __()-Aufrufe in settings.php angepasst
* Zwei nicht übereinstimmende Strings korrigiert: 'Configure how often...' und 'Update data is cached for...'
* Project-Id-Version in allen .po- und .pot-Dateien auf 0.0.6 aktualisiert

= 0.0.5 =
* Fehler bei Ordnerumbenennung bei Bulk-Updates (update-core.php) und WP Auto-Updates behoben
* Fallback-Matching über extrahierten Quellordnernamen hinzugefügt
* Rename-Logik in private rename_source()-Methode ausgelagert

= 0.0.4 =
* wp_redirect() durch wp_safe_redirect() ersetzt.
* wp_unslash() und absint() für POST-Input hinzugefügt.
* translators-Kommentare hinzugefügt.
* rename() durch WP_Filesystem::move() ersetzt.
* phpcs:ignore für direkte DB-Queries hinzugefügt.

= 0.0.3 =
* Verschachtelte Formular-Elemente behoben.
* Per-Plugin-Toggles speichern sofort via AJAX.
* Globaler Toggle spiegelt Zustand sofort.
* Debug-Ausgaben entfernt.

= 0.0.2 =
* Auto-Update-Toggle pro Plugin hinzugefügt.
* Manuellen Re-Check-Button hinzugefügt.
* Ein-Klick-Aktualisieren-Button hinzugefügt.
* Versionserkennung korrigiert.
* Transient-Key-Fehler behoben.
* Dark-Mode-Kompatibilität behoben.

= 0.0.1 =
* Erste Veröffentlichung.
