# Changelog

Alle wesentlichen Änderungen am AWDev Plugins Updater sind hier dokumentiert.
Das Format orientiert sich an [Keep a Changelog](https://keepachangelog.com/de/1.1.0/).

---

## [0.1.0] — 2026-03-17

### Behoben
- `awdev_fetch_api_data()`: API-Responses mit leerem oder `null`-Body werden jetzt explizit als Fehler behandelt und als `false` gecacht — zuvor gab `json_decode()` `null` zurück ohne den JSON-Fehler-Check auszulösen, was dazu führte dass `null` in den Transient geschrieben wurde
- `saveToggle()` in `settings.js`: fehlende `.then()`/`.catch()`-Kette sorgte dafür dass Speicher-Fehler lautlos ignoriert wurden; der Toggle wird jetzt visuell zurückgesetzt wenn der AJAX-Request fehlschlägt
- Re-Check-Button: Versions-Zelle wird jetzt *vor* dem Fetch auf `…` zurückgesetzt und zeigt `?` bei Fehler statt dauerhaft auf `…` stecken zu bleiben
- `compareVersions()`: `Number()` durch `parseInt()` ersetzt, damit Pre-Release-Suffixe wie `-beta` sicher ignoriert werden statt `NaN` zu produzieren und fälschlicherweise ein Update anzuzeigen

### Hinzugefügt
- `register_activation_hook()` ruft jetzt `awdev_activate()` auf um Standard-Optionen bei der Erstaktivierung zu setzen
- `awdev_sync_auto_update_defaults()`: neue Funktion die fehlende `awdev_auto_updates`-Einträge für Built-in-Plugins ergänzt ohne bestehende zu überschreiben; an `admin_init` gebunden, damit neu hinzugefügte Built-in-Plugins auf bestehenden Installationen ohne Deaktivieren/Aktivieren übernommen werden
- `escHtml()` in `settings.js` escapt jetzt auch Apostrophe (`'` → `&#039;`) für vollständige Sicherheit in Attribut- und Text-Kontexten

### Geändert
- `awdev_activate()` delegiert die Defaults-Einrichtung an `awdev_sync_auto_update_defaults()` um Duplikate zu vermeiden
- `AWDev_Updater::get_remote_data()` delegiert alle HTTP/Cache-Logik an den gemeinsamen `awdev_fetch_api_data()`-Helper — kein duplizierter Transient/HTTP-Code mehr
- Ungenutzte jQuery-Abhängigkeit aus `wp_enqueue_script()` entfernt — `settings.js` verwendet ausschließlich Vanilla JS
- JS-Unicode-Escapes (`\u2013`, `\u2014`) durch literale UTF-8-Zeichen in PHP-Dateien ersetzt

### Ebenfalls in dieser Version (von 0.0.9 übernommen)
- `json_last_error()`-Validierung nach jedem `json_decode()`-Aufruf
- `error_log()`-Ausgabe wenn `WP_Filesystem::move()` bei Ordner-Umbenennung fehlschlägt
- Tages-Anzeige für "zuletzt geprüft" — Intervalle über 24 h zeigen z. B. "vor 3 Tagen"
- Autor-Feld im "Version Details"-Popup liest aus der API-Response; Fallback auf Standard
- Eigenständige `uninstall.php` — alle `awdev_*`-Optionen und Transients beim Löschen entfernt
- Built-in Plugin-Registry in `awdev_built_in_plugins()` zentralisiert
- GitHub Actions Workflow `generate-l10n.yml` — kompiliert `.po` → `.l10n.php` automatisch via WP-CLI
- `awdev_force_option()`-Muster durch `update_option()` ersetzt
- `register_uninstall_hook()` und inline `awdev_uninstall()` entfernt; ersetzt durch `uninstall.php`

---

## [0.0.8]

### Behoben
- Auto-Update-Filter gab `null` statt `true` für AWDev-Plugins zurück — WP-Hintergrund-Updates wurden dadurch lautlos übersprungen
- Ordnerumbenennung nach ZIP-Extraktion schlug lautlos fehl wenn Zielordner bereits existierte

---

## [0.0.7]

### Geändert
- DarkAdmin-Kompatibilität auf der Einstellungsseite verbessert
- Farbbezogene `!important`-Deklarationen aus Basis-Selektoren entfernt, damit DarkAdmin-Styles korrekt durchgreifen
- DarkAdmin-spezifische Override-Regeln für stabiles Dark-Mode-Rendering beibehalten

---

## [0.0.6]

### Behoben
- Sprachdatei-`msgid`-Strings an die exakten `__()`-Aufrufe in `settings.php` angepasst
- Zwei nicht übereinstimmende Strings korrigiert: `Configure how often…` und `Update data is cached for…`
- `Project-Id-Version` in allen `.po`- und `.pot`-Dateien auf `0.0.6` aktualisiert

---

## [0.0.5]

### Behoben
- Fehler bei Ordnerumbenennung bei Bulk-Updates (`update-core.php`) und WP Auto-Updates wenn `hook_extra['plugin']` nicht befüllt ist
- Fallback-Matching über extrahierten Quellordnernamen hinzugefügt

### Geändert
- Rename-Logik in private `rename_source()`-Methode ausgelagert um Duplikate zu vermeiden

---

## [0.0.4]

### Geändert
- `wp_redirect()` durch `wp_safe_redirect()` ersetzt
- `wp_unslash()` und `absint()` für alle POST-Input-Sanitierung hinzugefügt
- `translators`-Kommentare für alle `_n()`- und `printf()`-i18n-Aufrufe hinzugefügt
- Direktes `rename()` durch `WP_Filesystem::move()` für Ordnerkorrektur nach ZIP-Extraktion ersetzt
- `phpcs:ignore` für intentionale direkte DB-Queries hinzugefügt

---

## [0.0.3]

### Behoben
- Illegal verschachtelte Formular-Elemente, die dazu führten dass "Einstellungen speichern" die falsche Action übermittelte

### Hinzugefügt
- Per-Plugin-Auto-Update-Toggles speichern sofort via AJAX — kein Speichern-Button nötig
- Globaler Toggle spiegelt Zustand sofort auf alle Per-Plugin-Toggles und speichert via AJAX

### Geändert
- Debug-Ausgaben aus der Gespeichert-Meldung entfernt
- "Einstellungen speichern"-Button in die Auto-Update-Settings-Karte verschoben

---

## [0.0.2]

### Hinzugefügt
- Auto-Update-Toggle pro Plugin
- Manueller Re-Check-Button pro Plugin
- Ein-Klick-Aktualisieren-Button bei verfügbarer neuerer Version

### Behoben
- Lokale Versionserkennung (Ordnername-Fallback)
- Transient-Key-Fehler der zur Anzeige von Bindestrichen führte
- Dark-Mode-Kompatibilität mit DarkAdmin
- Remote-Version wird jetzt beim Laden der Einstellungsseite aktiv abgerufen wenn kein Transient existiert

---

## [0.0.1]

### Hinzugefügt
- Erste Veröffentlichung
- Native WordPress-Update-Hook-Integration
- Einstellungsseite mit verwalteter Plugin-Tabelle und Cache-Flush
- Integrierte DarkAdmin-Unterstützung
- Übersetzungen: de\_DE, de\_AT, en\_US
