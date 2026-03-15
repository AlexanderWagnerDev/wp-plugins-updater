# AWDev Plugins Updater

![License: GPLv2](https://img.shields.io/badge/License-GPLv2-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b)
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
- ✅ “View version details” popup in the WP update screen
- ✅ Automatic plugin folder name fix after ZIP extraction
- ✅ Full Dark Mode support via DarkAdmin — settings page adapts automatically
- ✅ Translations: `de_DE`, `de_AT`, `en_US`

## Installation

1. Download the latest `awdev-plugin-updater.zip` from [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases).
2. In WordPress go to *Plugins → Add New → Upload Plugin* and select the ZIP.
3. Activate the plugin.
4. Open *Settings → AWDev Plugins Updater* — DarkAdmin is already registered automatically.

## Managing Plugins

Open *Settings → AWDev Plugins Updater*:

- **Built-in plugins** (AWDev Plugins Updater, DarkAdmin) are always registered automatically
- Click **Add Plugin** to register additional plugins by providing the basename (e.g. `my-plugin/my-plugin.php`) and API slug
- Use the **toggle** in the Auto-Update column to enable or disable automatic WP updates per plugin — saves instantly
- The **global toggle** in the Auto-Update Settings card applies to all plugins at once
- Use the **🔄 re-check button** to force a fresh API fetch for a single plugin
- The **Update button** appears automatically when a newer version is available on the server

## Changelog

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

Hält [AlexanderWagnerDev](https://alexanderwagnerdev.com) Plugins aktuell — ohne WordPress.org. Updates werden von einem selbst gehosteten Server ausgeliefert, damit jedes Release auf eigenem Zeitplan erscheint.

### Funktionen

- ✅ Native WordPress-Update-Integration — Updates erscheinen im Standard-Plugins-Screen
- ✅ Einstellungsseite unter *Einstellungen → AWDev Plugins Updater*
- ✅ DarkAdmin integriert — wird automatisch registriert wenn installiert
- ✅ **Auto-Update-Toggle** pro Plugin — wird sofort beim Klick gespeichert, kein Speichern-Button nötig
- ✅ **Globaler Auto-Update-Hauptschalter** — überträgt den Zustand sofort auf alle Per-Plugin-Toggles
- ✅ **Manueller Re-Check-Button** pro Plugin — leert den Transient und holt sofort die neueste Version
- ✅ **Ein-Klick-Update-Button** — erscheint automatisch wenn eine neuere Remote-Version verfügbar ist
- ✅ Weitere Plugins über die Einstellungsseite hinzufügen/entfernen — kein Code-Edit nötig
- ✅ Konfigurierbares Update-Cache-Intervall (1h–168h, Standard 6h)
- ✅ Manueller Cache-Flush-Button
- ✅ „Versions-Details“-Popup im WordPress-Update-Screen
- ✅ Automatische Korrektur des Plugin-Ordnernamens nach ZIP-Extraktion
- ✅ Vollständige Dark-Mode-Unterstützung via DarkAdmin — Einstellungsseite passt sich automatisch an
- ✅ Übersetzungen: `de_DE`, `de_AT`, `en_US`

### Installation

1. Neuste `awdev-plugin-updater.zip` aus den [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases) herunterladen.
2. In WordPress *Plugins → Neu hinzufügen → Plugin hochladen* aufrufen und die ZIP auswählen.
3. Plugin aktivieren.
4. *Einstellungen → AWDev Plugins Updater* öffnen — DarkAdmin ist bereits automatisch eingetragen.

### Plugins verwalten

*Einstellungen → AWDev Plugins Updater* öffnen:

- **Integrierte Plugins** (AWDev Plugins Updater, DarkAdmin) werden immer automatisch registriert
- Auf **Plugin hinzufügen** klicken um weitere Plugins per Basename und API-Slug einzutragen
- Den **Toggle** in der Auto-Update-Spalte nutzen um automatische WP-Updates pro Plugin zu steuern — wird sofort gespeichert
- Den **globalen Toggle** nutzen um alle Plugins auf einmal zu steuern
- Den **🔄 Re-Check-Button** nutzen um einen einzelnen Plugin-Transient sofort zu leeren und neu zu laden
- Der **Aktualisieren-Button** erscheint automatisch wenn eine neuere Version auf dem Server verfügbar ist

### Changelog

#### 0.0.3
- Fehler mit verschachtelten `<form>`-Elementen behoben — „Einstellungen speichern“ sendete vorher die falsche Aktion
- Per-Plugin-Auto-Update-Toggles werden jetzt sofort via AJAX gespeichert — kein Speichern-Button nötig
- Globaler Auto-Update-Toggle spiegelt Zustand sofort auf alle Per-Plugin-Toggles und speichert via AJAX
- Debug-Ausgaben aus der Erfolgs-Meldung entfernt
- „Einstellungen speichern“-Button in die Auto-Update-Settings-Card verschoben

#### 0.0.2
- Auto-Update-Toggle pro Plugin hinzugefügt (gespeichert in `awdev_auto_updates`)
- Manuellen Re-Check-Button pro Plugin hinzugefügt
- Ein-Klick-Aktualisieren-Button bei verfügbarer neuerer Version hinzugefügt
- Erkennung der lokal installierten Version korrigiert (Ordner-Fallback)
- Transient-Key-Fehler für DarkAdmin-Versionsanzeige behoben
- Dark-Mode-Styling-Kompatibilität mit DarkAdmin behoben
- Remote-Version wird beim Öffnen der Einstellungsseite aktiv abgerufen wenn kein Transient vorhanden

#### 0.0.1
- Erste Veröffentlichung
- Native WordPress-Update-Hook-Integration
- Einstellungsseite mit konfigurierbarer API-URL, Plugin-Tabelle und Cache-Flush
- Integrierte DarkAdmin-Unterstützung
- Übersetzungen: `de_DE`, `de_AT`, `en_US`

### Lizenz

GPLv2 oder höher — siehe [LICENSE](LICENSE)
