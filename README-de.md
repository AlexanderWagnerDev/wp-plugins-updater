# AWDev Plugins Updater

**Self-hosted Update-Manager fuer AlexanderWagnerDev Plugins - ohne WordPress.org.**

> Version 0.1.2 | Erfordert WordPress 6.3+ | Erfordert PHP 7.4+ | Lizenz: GPLv2

---

## Was es macht

AWDev Plugins Updater integriert sich nahtlos in das native WordPress-Update-System. Verwaltete Plugins erscheinen in der Standard-Ansicht **Dashboard > Updates** und in der **Plugins**-Liste - mit Versions-Badges, Ein-Klick-Updates und automatischen Hintergrund-Updates.

---

## Features

- Native WordPress-Update-Hook-Integration (keine eigenen Update-Seiten)
- Per-Plugin-Auto-Update-Toggles mit sofortigem AJAX-Speichern
- Globaler Auto-Update-Ein/Aus-Schalter
- Konfigurierbares Prüfintervall (1-168 Stunden, Standard 6 h)
- Manueller Ein-Klick-Re-Check pro Plugin
- Ein-Klick-Aktualisieren-Button in der Einstellungstabelle wenn eine neue Version verfügbar ist
- "Version Details"-Popup mit Changelog, Requires, Tested, Autor
- Integrierte Unterstützung für DarkAdmin - Dark Mode for Adminpanel
- Übersetzungen: de\_DE, de\_AT, en\_US

---

## Integrierte verwaltete Plugins

| Plugin | API-Slug |
|---|---|
| AWDev Plugins Updater | `awdev-plugins-updater` |
| DarkAdmin - Dark Mode for Adminpanel | `darkadmin-dark-mode-for-adminpanel` |

---

## Installation

1. Neuestes Release-ZIP von [GitHub Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases) herunterladen
2. Via **Plugins > Neu hinzufügen > Plugin hochladen** hochladen oder nach `wp-content/plugins/awdev-plugins-updater/` entpacken
3. Plugin aktivieren
4. Zu **Einstellungen > AWDev Plugins Updater** navigieren

---

## Einstellungen

| Option | Beschreibung |
|---|---|
| Auto-Update (alle Plugins) | Master-Schalter - deaktivieren verhindert alle AWDev Auto-Updates unabhängig von Per-Plugin-Toggles |
| Per-Plugin Auto-Update | Individueller Toggle pro verwaltetem Plugin |
| Prüfintervall | Wie oft der Updater den Update-Server abfragt (1-168 h) |
| Update-Cache leeren | Erzwingt sofortigen Re-Check beim nächsten Seitenaufruf |

---

## Wie Updates funktionieren

1. AWDev Plugins Updater hängt sich in `pre_set_site_transient_update_plugins` ein
2. Versions-Metadaten werden von der AWDev Plugins Updater API abgerufen
3. Wenn eine neuere Version verfügbar ist wird ein Update-Objekt in den WordPress-Transient injiziert
4. WordPress übernimmt Download, Entpacken und Installation nativ
5. `upgrader_source_selection` korrigiert den Ordnernamen nach der Extraktion bei Bedarf

---

## Changelog

Siehe [CHANGELOG-de.md](CHANGELOG-de.md) für die vollständige Historie.

**0.1.2** — Error-Logging-Aufrufe entfernt  
**0.1.1** — Fix Rename-in-sich-selbst-Absturz bei flacher ZIP-Extraktion, plugins_loaded Priorität 20, get_plugins()-Caching  
**0.1.0** — Mehrere Bugfixes und Verbesserungen (siehe CHANGELOG-de.md)  
**0.0.8** — Fix Auto-Update-Filter null-Rückgabe; Fix lautlose Rename-Fehler  

---

## Lizenz

GPLv2 oder höher - siehe [LICENSE](LICENSE)
