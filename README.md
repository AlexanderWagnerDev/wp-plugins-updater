# AWDev Plugin Updater

![License: GPLv2](https://img.shields.io/badge/License-GPLv2-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)

Keeps [AlexanderWagnerDev](https://alexanderwagnerdev.com) plugins up to date — without WordPress.org. Updates are served from a self-hosted server, so every release ships on your own schedule.

## Features

- ✅ Native WordPress update integration — updates appear in the standard Plugins screen
- ✅ Settings page under *Settings → AWDev Updater*
- ✅ DarkAdmin built-in — auto-registered when installed
- ✅ Add/remove additional plugins via the Settings UI — no code changes needed
- ✅ Configurable API base URL
- ✅ Manual cache flush button
- ✅ 6-hour update cache per plugin
- ✅ "View version details" popup in the WP update screen
- ✅ Automatic plugin folder name fix after ZIP extraction
- ✅ Dark Mode compatible via DarkAdmin CSS variables
- ✅ Translations: `de_DE`, `de_AT`, `en_US`

## Installation

1. Download the latest `awdev-plugin-updater.zip` from [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases).
2. In WordPress go to *Plugins → Add New → Upload Plugin* and select the ZIP.
3. Activate the plugin.
4. Open *Settings → AWDev Updater* — DarkAdmin is already registered automatically.

## Managing Plugins

Open *Settings → AWDev Updater* and click **Add Plugin**:

- **Plugin basename** — e.g. `my-plugin/my-plugin.php`
- **API slug** — the identifier used to resolve the update endpoint

## Changelog

### 1.0.0
- Initial release
- Native WordPress update hook integration
- Settings page with configurable API URL, managed plugin table and cache flush
- Built-in DarkAdmin support
- Translations: `de_DE`, `de_AT`, `en_US`

## License

GPLv2 or later — see [LICENSE](LICENSE)

---

## Deutsch

Hält [AlexanderWagnerDev](https://alexanderwagnerdev.com) Plugins aktuell — ohne WordPress.org. Updates werden von einem selbst gehosteten Server ausgeliefert, damit jedes Release auf eigenem Schedule erscheint.

### Funktionen

- ✅ Native WordPress-Update-Integration — Updates erscheinen im Standard-Plugins-Screen
- ✅ Einstellungsseite unter *Einstellungen → AWDev Updater*
- ✅ DarkAdmin integriert — wird automatisch registriert wenn installiert
- ✅ Weitere Plugins über die Einstellungsseite hinzufügen/entfernen — kein Code-Edit nötig
- ✅ Konfigurierbare API-Basis-URL
- ✅ Manueller Cache-Flush-Button
- ✅ 6-Stunden-Update-Cache pro Plugin
- ✅ „Version details“-Popup im WP-Update-Screen
- ✅ Automatische Korrektur des Plugin-Ordnernamens nach ZIP-Extraktion
- ✅ Dark-Mode-kompatibel über DarkAdmin CSS-Variablen
- ✅ Übersetzungen: `de_DE`, `de_AT`, `en_US`

### Installation

1. Neuste `awdev-plugin-updater.zip` aus den [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases) herunterladen.
2. In WordPress *Plugins → Neu hinzufügen → Plugin hochladen* aufrufen und die ZIP auswählen.
3. Plugin aktivieren.
4. *Einstellungen → AWDev Updater* öffnen — DarkAdmin ist bereits automatisch eingetragen.

### Plugins verwalten

*Einstellungen → AWDev Updater* öffnen und auf **Plugin hinzufügen** klicken:

- **Plugin Basename** — z.B. `mein-plugin/mein-plugin.php`
- **API Slug** — der Bezeichner für den Update-Endpunkt
