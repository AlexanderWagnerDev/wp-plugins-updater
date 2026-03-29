=== AWDev Plugins Updater ===
Contributors: alexanderwagnerdev
Tags: updater, awdev, plugin update, wordpress update
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: dev2026032901
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
