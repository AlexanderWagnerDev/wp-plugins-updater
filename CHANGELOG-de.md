# Changelog

Alle wesentlichen Änderungen am AWDev Plugins Updater sind hier dokumentiert.
Das Format orientiert sich an [Keep a Changelog](https://keepachangelog.com/de/1.1.0/).

---

## [0.1.2] — 2026-03-18

### Entfernt
- Error-Logging-Aufrufe entfernt

---

## [0.1.1] — 2026-03-17

### Behoben
- `fix_folder_name()`: Absturz wenn der ZIP-Inhalt ohne Unterordner extrahiert wird (flache Struktur) — `rename()` wurde mit einem Zielpfad *innerhalb* des Quellverzeichnisses aufgerufen, was zu `Invalid argument` fuehrte; Quell- und Remote-Source-Pfade werden jetzt normalisiert und verglichen, um flache ZIPs zu erkennen und die Umbenennung in ein Geschwisterverzeichnis umzuleiten
- `plugins_loaded`-Hook-Priorität auf `20` erhöht, damit Update-Filter erst nach vollständigem Laden aller anderen Plugins registriert werden

### Geändert
- `rename_source()` erhält jetzt den vollständig aufgelösten Zielpfad direkt statt `$remote_source`, sodass der Zielpfad nicht mehr fehlerhaft berechnet werden kann
- `awdev_get_local_version()` cached das `get_plugins()`-Ergebnis via `wp_cache_get/set` (Gruppe `awdev_updater`) — `get_plugins()` liest das Dateisystem jetzt höchstens einmal pro Request statt einmal pro verwaltetem Plugin

---

## [0.1.0] — 2026-03-17

### Behoben
- `awdev_fetch_api_data()`: API-Responses mit leerem oder `null`-Body werden jetzt explizit als Fehler behandelt und als `false` gecacht
- `saveToggle()` in `settings.js`: fehlende `.then()`/`.catch()`-Kette sorgte dafür dass Speicher-Fehler lautlos ignoriert wurden; der Toggle wird jetzt visuell zurückgesetzt wenn der AJAX-Request fehlschlägt
- Re-Check-Button: Versions-Zelle wird jetzt *vor* dem Fetch auf `...` zurückgesetzt und zeigt `?` bei Fehler
- `compareVersions()`: `Number()` durch `parseInt()` ersetzt damit Pre-Release-Suffixe wie `-beta` sicher ignoriert werden

### Hinzugefügt
- `register_activation_hook()` ruft jetzt `awdev_activate()` auf um Standard-Optionen bei der Erstaktivierung zu setzen
- `awdev_sync_auto_update_defaults()`: neue Funktion die fehlende `awdev_auto_updates`-Einträge für Built-in-Plugins ergänzt ohne bestehende zu überschreiben
- `escHtml()` in `settings.js` escapt jetzt auch Apostrophe für vollständige Sicherheit

### Geändert
- `awdev_activate()` delegiert die Defaults-Einrichtung an `awdev_sync_auto_update_defaults()`
- `AWDev_Updater::get_remote_data()` delegiert alle HTTP/Cache-Logik an den gemeinsamen `awdev_fetch_api_data()`-Helper
- Ungenutzte jQuery-Abhängigkeit aus `wp_enqueue_script()` entfernt
- JS-Unicode-Escapes durch literale UTF-8-Zeichen in PHP-Dateien ersetzt

### Ebenfalls in dieser Version (von 0.0.9 übernommen)
- `json_last_error()`-Validierung nach jedem `json_decode()`-Aufruf
- `error_log()`-Ausgabe wenn `WP_Filesystem::move()` bei Ordner-Umbenennung fehlschlägt
- Tages-Anzeige für "zuletzt geprüft"
- Autor-Feld im "Version Details"-Popup liest aus der API-Response
- Eigenständige `uninstall.php`
- Built-in Plugin-Registry in `awdev_built_in_plugins()` zentralisiert
- GitHub Actions Workflow `generate-l10n.yml`
- `awdev_force_option()`-Muster durch `update_option()` ersetzt
- `register_uninstall_hook()` entfernt; ersetzt durch `uninstall.php`

---

## [0.0.8]

### Behoben
- Auto-Update-Filter gab `null` statt `true` für AWDev-Plugins zurück
- Ordnerumbenennung nach ZIP-Extraktion schlug lautlos fehl wenn Zielordner bereits existierte

---

## [0.0.7]

### Geändert
- DarkAdmin-Kompatibilität auf der Einstellungsseite verbessert
- Farbbezogene `!important`-Deklarationen aus Basis-Selektoren entfernt
- DarkAdmin-spezifische Override-Regeln für stabiles Dark-Mode-Rendering beibehalten

---

## [0.0.6]

### Behoben
- Sprachdatei-`msgid`-Strings an die exakten `__()`-Aufrufe in `settings.php` angepasst
- Zwei nicht übereinstimmende Strings korrigiert
- `Project-Id-Version` in allen `.po`- und `.pot`-Dateien auf `0.0.6` aktualisiert

---

## [0.0.5]

### Behoben
- Fehler bei Ordnerumbenennung bei Bulk-Updates und WP Auto-Updates
- Fallback-Matching über extrahierten Quellordnernamen hinzugefügt

### Geändert
- Rename-Logik in private `rename_source()`-Methode ausgelagert

---

## [0.0.4]

### Geändert
- `wp_redirect()` durch `wp_safe_redirect()` ersetzt
- `wp_unslash()` und `absint()` für alle POST-Input-Sanitierung hinzugefügt
- `translators`-Kommentare für alle `_n()`- und `printf()`-i18n-Aufrufe hinzugefügt
- Direktes `rename()` durch `WP_Filesystem::move()` ersetzt
- `phpcs:ignore` für intentionale direkte DB-Queries hinzugefügt

---

## [0.0.3]

### Behoben
- Illegal verschachtelte Formular-Elemente

### Hinzugefügt
- Per-Plugin-Auto-Update-Toggles speichern sofort via AJAX
- Globaler Toggle spiegelt Zustand sofort auf alle Per-Plugin-Toggles

### Geändert
- Debug-Ausgaben entfernt
- "Einstellungen speichern"-Button verschoben

---

## [0.0.2]

### Hinzugefügt
- Auto-Update-Toggle pro Plugin
- Manueller Re-Check-Button pro Plugin
- Ein-Klick-Aktualisieren-Button bei verfügbarer neuerer Version

### Behoben
- Lokale Versionserkennung (Ordnername-Fallback)
- Transient-Key-Fehler
- Dark-Mode-Kompatibilität mit DarkAdmin
- Remote-Version wird beim Laden der Einstellungsseite aktiv abgerufen

---

## [0.0.1]

### Hinzugefügt
- Erste Veröffentlichung
- Native WordPress-Update-Hook-Integration
- Einstellungsseite mit verwalteter Plugin-Tabelle und Cache-Flush
- Integrierte DarkAdmin-Unterstützung
- Übersetzungen: de\_DE, de\_AT, en\_US
