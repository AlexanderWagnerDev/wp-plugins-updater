=== AWDev Plugin Updater ===
Contributors: alexanderwagnerdev
Tags: updater, self-hosted, plugin update, update manager
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Self-hosted update manager for AlexanderWagnerDev plugins. Delivers updates from a custom domain instead of WordPress.org.

== Description ==
AWDev Plugin Updater replaces the WordPress.org update channel for AlexanderWagnerDev plugins with a self-hosted update server. This gives full control over versioning, distribution and release timing — completely independent of the WordPress.org review queue.

The updater hooks directly into the native WordPress update system, so updates appear in the standard WordPress Plugins screen just like any other plugin update. No third-party update libraries are used.

Features:
* Native WordPress update integration (no third-party update libraries)
* Settings page under Settings > AWDev Updater
* Configurable API base URL
* Built-in support for DarkAdmin - Dark Mode for Adminpanel
* Add additional AlexanderWagnerDev plugins via the Settings UI
* Manual cache flush button
* 6-hour update cache per plugin (transient-based)
* "View version details" popup support in WP update screen
* Automatic plugin folder name fix after ZIP extraction
* DarkAdmin Dark Mode compatible settings page UI
* Translations: de_DE, de_AT, en_US

== Installation ==
1. Download the latest awdev-plugin-updater.zip from the GitHub Releases page.
2. Upload via Plugins > Add New > Upload Plugin or extract to /wp-content/plugins/awdev-plugin-updater/.
3. Activate the plugin through the Plugins screen in WordPress.
4. Go to Settings > AWDev Updater to verify the server URL and managed plugins.

== Frequently Asked Questions ==
= Do I need to configure anything? =
No. DarkAdmin is registered automatically when installed. The default server URL is pre-configured.

= Can I add other plugins? =
Yes. Use the Settings page to add any additional AlexanderWagnerDev plugin by providing its basename (e.g. my-plugin/my-plugin.php) and API slug.

= How often does WordPress check for updates? =
WordPress checks approximately every 12 hours by default. The updater caches API responses for 6 hours. Use the "Flush Update Cache" button on the Settings page to force an immediate re-check.

= Does this replace WordPress.org updates? =
Only for AlexanderWagnerDev plugins registered in this updater. All other plugins continue to update normally via WordPress.org.

= Is this compatible with Dark Mode? =
Yes. The settings page fully supports DarkAdmin and adapts to dark mode automatically.

== Changelog ==
= 1.0.0 =
* Initial release.
* Native WordPress update hook integration.
* Settings page with configurable API URL, managed plugin table and cache flush.
* Built-in DarkAdmin support.
* Translations: de_DE, de_AT, en_US.

== Deutsch ==
Der AWDev Plugin Updater ersetzt den WordPress.org-Update-Kanal für AlexanderWagnerDev-Plugins durch einen selbst gehosteten Update-Server. Damit hast du volle Kontrolle über Versionierung, Distribution und Release-Zeitpunkt — vollständig unabhängig von der WordPress.org-Review-Queue.

Der Updater klinkt sich direkt in das native WordPress-Update-System ein, sodass Updates im Standard-Plugins-Screen erscheinen — genau wie bei jedem anderen Plugin.

Funktionen:
* Native WordPress-Update-Integration (keine Drittanbieter-Bibliotheken)
* Einstellungsseite unter Einstellungen → AWDev Updater
* Konfigurierbare API-Basis-URL
* Integrierte Unterstützung für DarkAdmin – Dark Mode for Adminpanel
* Weitere AlexanderWagnerDev-Plugins über die Einstellungsseite hinzufügbar
* Manueller Cache-Flush-Button
* 6-Stunden-Update-Cache pro Plugin
* Dark-Mode-kompatible Einstellungsseite
* Übersetzungen: de_DE, de_AT, en_US

=== Installation ===
1. Neuste awdev-plugin-updater.zip von der GitHub-Releases-Seite herunterladen.
2. Über Plugins → Neu hinzufügen → Plugin hochladen installieren oder nach /wp-content/plugins/awdev-plugin-updater/ entpacken.
3. Plugin in WordPress unter „Plugins“ aktivieren.
4. Einstellungen → AWDev Updater aufrufen und Server-URL sowie verwaltete Plugins prüfen.

=== Häufig gestellte Fragen ===
= Muss ich etwas konfigurieren? =
Nein. DarkAdmin wird automatisch registriert wenn es installiert ist. Die Standard-Server-URL ist bereits voreingestellt.

= Kann ich weitere Plugins hinzufügen? =
Ja. Über die Einstellungsseite können beliebige weitere AlexanderWagnerDev-Plugins mit Basename (z.B. mein-plugin/mein-plugin.php) und API-Slug eingetragen werden.

= Wie oft prüft WordPress auf Updates? =
WordPress prüft standardmäßig ca. alle 12 Stunden. Der Updater cached API-Antworten 6 Stunden. Über den "Update-Cache leeren"-Button auf der Einstellungsseite kann eine sofortige Neuprüfung erzwungen werden.

= Ersetzt das die WordPress.org-Updates? =
Nur für AlexanderWagnerDev-Plugins die in diesem Updater registriert sind. Alle anderen Plugins werden weiterhin normal über WordPress.org aktualisiert.

= Ist das mit Dark Mode kompatibel? =
Ja. Die Einstellungsseite unterstützt DarkAdmin vollständig und passt sich automatisch dem Dark Mode an.

=== Changelog ===
= 1.0.0 =
* Erste Veröffentlichung.
* Native WordPress-Update-Hook-Integration.
* Einstellungsseite mit konfigurierbarer API-URL, Plugin-Tabelle und Cache-Flush.
* Integrierte DarkAdmin-Unterstützung.
* Übersetzungen: de_DE, de_AT, en_US.
