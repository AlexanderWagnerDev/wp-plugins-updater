=== AWDev Plugins Updater ===
Contributors: alexanderwagnerdev
Tags: updater, self-hosted, plugin update, update manager, dark mode
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Keeps AlexanderWagnerDev plugins up to date — without WordPress.org. Updates are served from a self-hosted server, so every release ships on your own schedule.

== Description ==
AWDev Plugins Updater replaces the WordPress.org update channel for AlexanderWagnerDev plugins with a self-hosted update server. This gives full control over versioning, distribution and release timing — completely independent of the WordPress.org review queue.

The updater hooks directly into the native WordPress update system, so updates appear in the standard Plugins screen just like any other plugin update. No third-party update libraries are used.

Features:
* Native WordPress update integration (no third-party libraries)
* Settings page under Settings > AWDev Plugins Updater
* Built-in support for DarkAdmin – Dark Mode for Adminpanel (auto-registered)
* Per-plugin auto-update toggle — enable or disable automatic updates per plugin
* Manual re-check button per plugin — clears transient and fetches latest version immediately
* One-click Update button — appears automatically when a newer remote version is available
* Add additional plugins via the Settings UI — no code changes needed
* 6-hour update cache per plugin (transient-based)
* Manual full cache flush button
* "View version details" popup support in WP update screen
* Automatic plugin folder name fix after ZIP extraction
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
Each plugin has an Auto-Update toggle on the Settings page. When enabled, WordPress will automatically install updates for that plugin during the regular background update cycle. When disabled, updates must be applied manually.

= How do I manually trigger an update check? =
Click the re-check button (circular arrow icon) next to any plugin on the Settings page. This clears the cached version data and fetches the latest version from the server immediately. An Update button will appear if a newer version is available.

= How often does WordPress check for updates? =
WordPress checks approximately every 12 hours by default. The updater caches API responses for 6 hours. Use the "Flush Update Cache" button on the Settings page to force an immediate full re-check of all plugins.

= Does this replace WordPress.org updates? =
Only for AlexanderWagnerDev plugins registered in this updater. All other plugins continue to update normally via WordPress.org.

= Is this compatible with Dark Mode? =
Yes. The settings page fully supports DarkAdmin and adapts to dark mode automatically.

== Changelog ==
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
Der AWDev Plugins Updater ersetzt den WordPress.org-Update-Kanal für AlexanderWagnerDev-Plugins durch einen selbst gehosteten Update-Server. Damit hast du volle Kontrolle über Versionierung, Distribution und Release-Zeitpunkt — vollständig unabhängig von der WordPress.org-Review-Queue.

Der Updater klinkt sich direkt in das native WordPress-Update-System ein, sodass Updates im Standard-Plugins-Screen erscheinen — genau wie bei jedem anderen Plugin.

Funktionen:
* Native WordPress-Update-Integration (keine Drittanbieter-Bibliotheken)
* Einstellungsseite unter Einstellungen → AWDev Plugins Updater
* Integrierte Unterstützung für DarkAdmin – Dark Mode for Adminpanel (automatisch registriert)
* Auto-Update-Toggle pro Plugin — automatische Updates einzeln aktivieren oder deaktivieren
* Manueller Re-Check-Button pro Plugin — leert den Transient und holt sofort die neueste Version
* Ein-Klick-Aktualisieren-Button — erscheint automatisch wenn eine neuere Remote-Version verfügbar ist
* Weitere Plugins über die Einstellungsseite hinzufügen — kein Code-Edit nötig
* 6-Stunden-Update-Cache pro Plugin
* Manueller Cache-Flush-Button
* Dark-Mode-kompatible Einstellungsseite — passt sich automatisch via DarkAdmin an
* Übersetzungen: de_DE, de_AT, en_US

=== Installation ===
1. Neuste awdev-plugin-updater.zip von der GitHub-Releases-Seite herunterladen.
2. Über Plugins → Neu hinzufügen → Plugin hochladen installieren oder nach /wp-content/plugins/awdev-plugin-updater/ entpacken.
3. Plugin in WordPress unter „Plugins“ aktivieren.
4. Einstellungen → AWDev Plugins Updater aufrufen und verwaltete Plugins prüfen.

=== Häufig gestellte Fragen ===
= Muss ich etwas konfigurieren? =
Nein. DarkAdmin wird automatisch registriert wenn es installiert ist. Die Standard-Server-URL ist bereits voreingestellt.

= Kann ich weitere Plugins hinzufügen? =
Ja. Über die Einstellungsseite können beliebige weitere AlexanderWagnerDev-Plugins mit Basename und API-Slug eingetragen werden.

= Wie funktioniert der Auto-Update-Toggle? =
Jedes Plugin hat einen Auto-Update-Toggle auf der Einstellungsseite. Wenn aktiviert, installiert WordPress Updates für dieses Plugin automatisch im Hintergrund. Wenn deaktiviert, müssen Updates manuell eingespielt werden.

= Wie löse ich manuell eine Update-Prüfung aus? =
Den Re-Check-Button (Pfeil-Symbol) neben dem jeweiligen Plugin auf der Einstellungsseite klicken. Damit wird der gecachte Versions-Transient gelöscht und sofort die neueste Version vom Server abgerufen. Ein Aktualisieren-Button erscheint wenn eine neuere Version verfügbar ist.

= Wie oft prüft WordPress auf Updates? =
WordPress prüft standardmäßig ca. alle 12 Stunden. Der Updater cached API-Antworten 6 Stunden. Über den „Update-Cache leeren“-Button kann eine sofortige Neuprüfung aller Plugins erzwungen werden.

= Ersetzt das die WordPress.org-Updates? =
Nur für AlexanderWagnerDev-Plugins die in diesem Updater registriert sind. Alle anderen Plugins werden weiterhin normal über WordPress.org aktualisiert.

= Ist das mit Dark Mode kompatibel? =
Ja. Die Einstellungsseite unterstützt DarkAdmin vollständig und passt sich automatisch dem Dark Mode an.

=== Changelog ===
= 0.0.2 =
* Auto-Update-Toggle pro Plugin hinzugefügt.
* Manuellen Re-Check-Button pro Plugin hinzugefügt.
* Ein-Klick-Aktualisieren-Button bei verfügbarer neuerer Version hinzugefügt.
* Erkennung der lokal installierten Version korrigiert.
* Transient-Key-Fehler für Versionsanzeige behoben.
* Dark-Mode-Styling-Kompatibilität mit DarkAdmin behoben.
* Remote-Version wird beim Öffnen der Einstellungsseite aktiv abgerufen.

= 0.0.1 =
* Erste Veröffentlichung.
* Native WordPress-Update-Hook-Integration.
* Einstellungsseite mit Plugin-Tabelle und Cache-Flush.
* Integrierte DarkAdmin-Unterstützung.
* Übersetzungen: de_DE, de_AT, en_US.
