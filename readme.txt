=== AWDev Plugin Updater ===
Contributors: alexanderwagnerdev
Tags: updater, self-hosted, plugin update, github
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Self-hosted update manager for AlexanderWagnerDev plugins. Delivers updates from wp-plugins-updates.awdev.space instead of WordPress.org.

== Description ==
AWDev Plugin Updater replaces the WordPress.org update channel for AlexanderWagnerDev plugins with a self-hosted update server at wp-plugins-updates.awdev.space. This gives full control over versioning, distribution and release timing — completely independent of the WordPress.org review queue.

The updater hooks directly into the native WordPress update system, so updates appear in the standard WordPress Plugins screen just like any other plugin update.

Features:
* Native WordPress update integration (no third-party update libraries)
* Settings page under Settings > AWDev Updater
* Configurable API base URL
* Built-in support for DarkAdmin - Dark Mode for Adminpanel
* Add additional AlexanderWagnerDev plugins via the Settings UI
* Manual cache flush button
* 6-hour update cache per plugin (transient-based)
* "View version details" popup support
* Automatic plugin folder name fix after ZIP extraction
* DarkAdmin Dark Mode compatible settings page UI

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/awdev-plugin-updater/`.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Go to Settings > AWDev Updater to verify the server URL and managed plugins.

== Frequently Asked Questions ==
= Do I need to configure anything? =
No. DarkAdmin is registered automatically when installed. The default server URL points to wp-plugins-updates.awdev.space.

= Can I add other plugins? =
Yes. Use the Settings page to add any additional AlexanderWagnerDev plugin by providing its basename (e.g. my-plugin/my-plugin.php) and API slug.

= How often does WordPress check for updates? =
WordPress checks approximately every 12 hours by default. The updater caches API responses for 6 hours. Use the "Flush Update Cache" button on the Settings page to force an immediate re-check.

= Does this replace WordPress.org updates? =
Only for AlexanderWagnerDev plugins registered in this updater. All other plugins continue to update normally via WordPress.org.

== Changelog ==
= 1.0.0 =
* Initial release.
* Native WordPress update hook integration.
* Settings page with configurable API URL, managed plugin table, cache flush.
* Built-in DarkAdmin support.
* German (de_DE, de_AT) translations included.

== Deutsch ==
Der AWDev Plugin Updater ersetzt den WordPress.org-Update-Kanal für AlexanderWagnerDev-Plugins durch einen selbst gehosteten Update-Server unter wp-plugins-updates.awdev.space. Damit hast du volle Kontrolle über Versionierung, Distribution und Release-Zeitpunkt — vollständig unabhängig von der WordPress.org-Review-Queue.

Funktionen:
* Native WordPress-Update-Integration
* Einstellungsseite unter Einstellungen → AWDev Updater
* Konfigurierbare API-Basis-URL
* Integrierte Unterstützung für DarkAdmin
* Weitere Plugins über die Einstellungsseite hinzufügbar
* Manueller Cache-Flush-Button
* 6-Stunden-Update-Cache pro Plugin
* Dark-Mode-kompatible Einstellungsseite

=== Installation ===
1. Plugin-Ordner nach `/wp-content/plugins/awdev-plugin-updater/` hochladen.
2. Plugin in WordPress unter „Plugins“ aktivieren.
3. Einstellungen → AWDev Updater aufrufen.

=== FAQ ===
= Muss ich etwas konfigurieren? =
Nein. DarkAdmin wird automatisch registriert, wenn es installiert ist. Die Standard-Server-URL zeigt auf wp-plugins-updates.awdev.space.

= Kann ich weitere Plugins hinzufügen? =
Ja. Über die Einstellungsseite können beliebige weitere AlexanderWagnerDev-Plugins mit Basename und API-Slug eingetragen werden.

=== Changelog ===
= 1.0.0 =
* Erste Veröffentlichung.
