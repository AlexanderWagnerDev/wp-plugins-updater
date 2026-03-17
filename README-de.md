# AWDev Plugins Updater

<div align="center">

![Version](https://img.shields.io/badge/version-0.0.9-blue?style=flat-square)
![WordPress](https://img.shields.io/badge/WordPress-6.3%2B-21759b?style=flat-square&logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![Lizenz](https://img.shields.io/badge/Lizenz-GPLv2-green?style=flat-square)
![Getestet bis](https://img.shields.io/badge/Getestet%20bis-WP%206.9-21759b?style=flat-square)

**Selbst gehosteter Update-Manager für [AlexanderWagnerDev](https://alexanderwagnerdev.com) Plugins.**  
Kein WordPress.org erforderlich — jede Version erscheint nach eigenem Zeitplan.

[Installation](#installation) · [Funktionen](#funktionen) · [Plugins verwalten](#plugins-verwalten) · [Changelog](#changelog) · [English](README.md)

</div>

---

## Überblick

Der AWDev Plugins Updater ersetzt den WordPress.org-Update-Kanal für AlexanderWagnerDev-Plugins durch einen selbst gehosteten Update-Server. Er klinkt sich direkt in das native WordPress-Update-System ein — Updates erscheinen im normalen **Plugins**-Bildschirm wie jedes andere Plugin-Update. Es werden keine Drittanbieter-Bibliotheken verwendet.

## Funktionen

| Funktion | Beschreibung |
|---|---|
| 🔄 Native WP-Integration | Updates erscheinen im Standard-Plugins-Bildschirm |
| ⚙️ Einstellungsseite | *Einstellungen → AWDev Plugins Updater* |
| 🌙 DarkAdmin integriert | Automatisch registriert, volle Dark-Mode-Unterstützung |
| 🛡️ Auto-Update-Toggle pro Plugin | Speichert sofort beim Klick — kein Speichern-Button |
| 🌍 Globaler Hauptschalter | Überträgt Zustand sofort auf alle Per-Plugin-Toggles |
| 🔄 Manueller Re-Check-Button | Leert Transient und ruft neueste Version sofort ab |
| ⬆️ Ein-Klick-Aktualisieren-Button | Erscheint automatisch wenn neue Version verfügbar ist |
| ➕ Plugins hinzufügen/entfernen | Über die Einstellungsseite — keine Codeänderungen |
| ⏱️ Konfigurierbares Cache-Intervall | 1h–168h, Standard 6h |
| 🗑️ Saubere Deinstallation | Alle Optionen und Transients beim Löschen entfernt |
| 🌎 Übersetzungen | `de_DE`, `de_AT`, `en_US` |

## Installation

1. Neueste `awdev-plugins-updater.zip` von [Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases) herunterladen.
2. In WordPress unter **Plugins → Neu hinzufügen → Plugin hochladen** die ZIP auswählen.
3. Plugin aktivieren.
4. **Einstellungen → AWDev Plugins Updater** öffnen — DarkAdmin ist bereits automatisch registriert.

## Plugins verwalten

Unter **Einstellungen → AWDev Plugins Updater**:

- **Built-in Plugins** (AWDev Plugins Updater, DarkAdmin) sind immer automatisch registriert
- Den **Toggle** in der Auto-Update-Spalte verwenden um Hintergrund-Updates pro Plugin zu aktivieren oder deaktivieren — speichert sofort via AJAX
- Der **globale Toggle** in der Auto-Update-Einstellungskarte gilt für alle Plugins gleichzeitig
- Den **🔄 Re-Check-Button** nutzen um für ein einzelnes Plugin einen frischen API-Abruf zu erzwingen
- Der **Aktualisieren-Button** erscheint automatisch wenn eine neuere Version auf dem Server verfügbar ist
- **Update-Cache leeren** erzwingt eine sofortige vollständige Neuprüfung aller Plugins

## Changelog

### 0.0.9 — 2026-03-17

- **Hinzugefügt** `json_last_error()`-Validierung nach jedem `json_decode()`-Aufruf — ungültige API-Responses werden als Fehler behandelt und via `error_log()` geloggt
- **Hinzugefügt** `error_log()`-Ausgabe wenn `WP_Filesystem::move()` bei der Ordner-Umbenennung fehlschlägt
- **Hinzugefügt** Tages-Anzeige für "zuletzt geprüft" — Intervalle über 24 h zeigen jetzt z. B. *"vor 3 Tagen"* statt *"vor 72 Stunden"*
- **Hinzugefügt** Autor-Feld im "Version Details"-Popup liest aus der API-Response; Fallback auf Standard
- **Hinzugefügt** Eigenständige [`uninstall.php`](uninstall.php) — alle `awdev_*`-Optionen und Transients werden beim Plugin-Löschen aus der DB entfernt; wird von WordPress direkt geladen ohne das Plugin zu initialisieren
- **Hinzugefügt** Built-in Plugin-Registry in `awdev_built_in_plugins()` zentralisiert — neues Plugin hinzufügen erfordert nur noch einen Eintrag
- **Geändert** `awdev_force_option()` `DELETE` + `add_option()`-Muster durch `update_option()` ersetzt

### 0.0.8

- **Behoben** Auto-Update-Filter gab `null` statt `true` für AWDev-Plugins zurück — WP-Hintergrund-Updates wurden dadurch lautlos übersprungen
- **Behoben** Ordnerumbenennung nach ZIP-Extraktion schlug lautlos fehl wenn Zielordner bereits existierte

### 0.0.7

- **Geändert** DarkAdmin-Kompatibilität auf der Einstellungsseite verbessert
- **Geändert** Farbbezogene `!important`-Deklarationen aus Basis-Selektoren entfernt
- **Geändert** DarkAdmin-spezifische Override-Regeln beibehalten

### 0.0.6

- **Behoben** Sprachdatei-`msgid`-Strings an `__()`-Aufrufe in `settings.php` angepasst
- **Behoben** Zwei nicht übereinstimmende Strings korrigiert
- **Behoben** `Project-Id-Version` in allen `.po`- und `.pot`-Dateien auf `0.0.6` aktualisiert

### 0.0.5

- **Behoben** Fehler bei Ordnerumbenennung bei Bulk-Updates und WP Auto-Updates
- **Hinzugefügt** Fallback-Matching über extrahierten Quellordnernamen
- **Geändert** Rename-Logik in private `rename_source()`-Methode ausgelagert

### 0.0.4

- **Geändert** `wp_redirect()` durch `wp_safe_redirect()` ersetzt
- **Geändert** `wp_unslash()` und `absint()` für POST-Input hinzugefügt
- **Geändert** `translators`-Kommentare für `_n()`- und `printf()`-Aufrufe hinzugefügt
- **Geändert** `rename()` durch `WP_Filesystem::move()` ersetzt
- **Geändert** `phpcs:ignore` für direkte DB-Queries hinzugefügt

### 0.0.3

- **Behoben** Illegal verschachtelte `<form>`-Elemente
- **Hinzugefügt** Per-Plugin-Toggles speichern sofort via AJAX
- **Hinzugefügt** Globaler Toggle spiegelt Zustand sofort auf alle Per-Plugin-Toggles
- **Geändert** Debug-Ausgaben entfernt
- **Geändert** Speichern-Button in Auto-Update-Settings-Karte verschoben

### 0.0.2

- **Hinzugefügt** Auto-Update-Toggle pro Plugin
- **Hinzugefügt** Manueller Re-Check-Button pro Plugin
- **Hinzugefügt** Ein-Klick-Aktualisieren-Button
- **Behoben** Versionserkennung (Ordnername-Fallback)
- **Behoben** Transient-Key-Fehler bei der DarkAdmin-Versionsanzeige
- **Behoben** Dark-Mode-Kompatibilität mit DarkAdmin
- **Behoben** Remote-Version wird beim Laden der Einstellungsseite aktiv abgerufen

### 0.0.1

- Erste Veröffentlichung
- Native WordPress-Update-Hook-Integration
- Einstellungsseite mit verwalteter Plugin-Tabelle und Cache-Flush
- Integrierte DarkAdmin-Unterstützung
- Übersetzungen: `de_DE`, `de_AT`, `en_US`

## Lizenz

Vertrieben unter der **GPLv2 oder später** Lizenz — siehe [LICENSE](LICENSE) für Details.

---

<div align="center">
Erstellt von <a href="https://alexanderwagnerdev.com">AlexanderWagnerDev</a> · <a href="https://github.com/AlexanderWagnerDev/wp-plugins-updater">GitHub</a>
</div>
