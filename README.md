# AWDev Plugin Updater

![License: GPLv2](https://img.shields.io/badge/License-GPLv2-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)

A self-hosted WordPress plugin updater for [AlexanderWagnerDev](https://alexanderwagnerdev.com) plugins. Updates are delivered from a custom domain instead of WordPress.org — giving full control over versioning, distribution and release timing.

---

## Features

- ✅ Native WordPress update integration — updates appear in the standard WP Plugins screen
- ✅ Settings page under *Settings → AWDev Updater*
- ✅ Configurable API base URL
- ✅ DarkAdmin built-in — auto-registered when installed
- ✅ Add/remove additional plugins dynamically via Settings UI
- ✅ Manual cache flush button
- ✅ 6-hour update cache per plugin (transient-based)
- ✅ "View version details" popup support in WP update screen
- ✅ Automatic plugin folder name fix after ZIP extraction
- ✅ Dark Mode compatible (uses DarkAdmin CSS variables)
- ✅ Translations: `de_DE`, `de_AT`, `en_US` included

---

## Installation

1. Download the latest `awdev-plugin-updater.zip` from [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases).
2. Upload via *Plugins → Add New → Upload Plugin* or extract to `/wp-content/plugins/awdev-plugin-updater/`.
3. Activate the plugin through the *Plugins* screen in WordPress.
4. Go to *Settings → AWDev Updater* to verify the server URL and managed plugins.

---

## Adding a New Plugin

Open *Settings → AWDev Updater* and click **Add Plugin**. Enter:

- **Plugin basename** — e.g. `my-plugin/my-plugin.php`
- **API slug** — e.g. `my-plugin`

No code changes required.

---

## Changelog

### 1.0.0
- Initial release
- Native WordPress update hook integration
- Settings page with configurable API URL, managed plugin table and cache flush
- Built-in DarkAdmin support
- Translations: `de_DE`, `de_AT`, `en_US`

---

## License

GPLv2 or later — see [LICENSE](LICENSE)

---

## Deutsch

Ein selbst gehosteter WordPress-Plugin-Updater für [AlexanderWagnerDev](https://alexanderwagnerdev.com) Plugins. Updates werden von einer eigenen Domain statt von WordPress.org ausgeliefert — für volle Kontrolle über Versionierung, Distribution und Release-Zeitpunkt.

### Funktionen

- ✅ Native WordPress-Update-Integration — Updates erscheinen im Standard-Plugins-Screen
- ✅ Einstellungsseite unter *Einstellungen → AWDev Updater*
- ✅ Konfigurierbare API-Basis-URL
- ✅ DarkAdmin integriert — wird automatisch registriert wenn installiert
- ✅ Weitere Plugins dynamisch über die Einstellungsseite hinzufügen/entfernen
- ✅ Manueller Cache-Flush-Button
- ✅ 6-Stunden-Update-Cache pro Plugin
- ✅ Dark-Mode-kompatible Einstellungsseite (nutzt DarkAdmin CSS-Variablen)
- ✅ Übersetzungen: `de_DE`, `de_AT`, `en_US` enthalten

### Installation

1. Neuste `awdev-plugin-updater.zip` aus den [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases) herunterladen.
2. Über *Plugins → Neu hinzufügen → Plugin hochladen* installieren oder nach `/wp-content/plugins/awdev-plugin-updater/` entpacken.
3. Plugin in WordPress unter *Plugins* aktivieren.
4. *Einstellungen → AWDev Updater* aufrufen und Server-URL sowie verwaltete Plugins prüfen.

### Plugin hinzufügen

*Einstellungen → AWDev Updater* öffnen und auf **Plugin hinzufügen** klicken:

- **Plugin Basename** — z.B. `mein-plugin/mein-plugin.php`
- **API Slug** — z.B. `mein-plugin`

### Changelog

#### 1.0.0
- Erste Veröffentlichung
- Native WordPress-Update-Hook-Integration
- Einstellungsseite mit konfigurierbarer API-URL, Plugin-Tabelle und Cache-Flush
- Integrierte DarkAdmin-Unterstützung
- Übersetzungen: `de_DE`, `de_AT`, `en_US`
