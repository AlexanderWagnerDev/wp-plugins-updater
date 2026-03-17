# Changelog

Alle wesentlichen Änderungen am AWDev Plugins Updater sind hier dokumentiert.
Das Format orientiert sich an [Keep a Changelog](https://keepachangelog.com/de/1.1.0/).

---

## [0.0.9] — 2026-03-17

### Hinzugefügt
- `json_last_error()`-Validierung nach jedem `json_decode()`-Aufruf — ungültige API-Responses werden als Fehler behandelt und via `error_log()` geloggt
- `error_log()`-Ausgabe wenn `WP_Filesystem::move()` bei der Ordner-Umbenennung fehlschlägt
- Tages-Anzeige für "zuletzt geprüft" — Intervalle über 24 h zeigen jetzt z. B. "vor 3 Tagen" statt "vor 72 Stunden"
- Autor-Feld im "Version Details"-Popup liest jetzt aus der API-Response; Fallback auf Hard-coded-Standard
- Eigenständige `uninstall.php` — alle `awdev_*`-Optionen und Transients werden beim Plugin-Löschen aus der Datenbank entfernt; wird von WordPress direkt geladen ohne das komplette Plugin zu initialisieren
- Built-in Plugin-Registry in `awdev_built_in_plugins()` zentralisiert — neues Plugin hinzufügen erfordert nur noch einen einzigen Eintrag
- GitHub Actions Workflow `generate-l10n.yml` — kompiliert `.po`-Dateien automatisch zu `.l10n.php` via WP-CLI bei jeder `.po`-Änderung; `.l10n.php`-Dateien werden ins Repository zurückgeführt und in die Release-ZIP eingepackt (Performance-Vorteil für WordPress 6.5+)

### Geändert
- `awdev_force_option()` `DELETE` + `add_option()`-Muster durch `update_option()` ersetzt um Race-Condition-Risiko zu eliminieren
- `register_uninstall_hook()` und inline `awdev_uninstall()` aus der Haupt-Plugin-Datei entfernt; ersetzt durch `uninstall.php`
- `.po`, `.pot`, `readme.txt`, `readme-de.txt` und `CHANGELOG*.txt` aus der Release-ZIP ausgeschlossen — diese sind Entwickler-/Dokumentationsdateien die zur Laufzeit nicht benötigt werden
- `Project-Id-Version` in allen `.po`- und `.pot`-Dateien auf `0.0.9` angehoben
- Fehlende `%d day ago` / `%d days ago` msgid-Einträge in allen `.po`- und `.pot`-Dateien nachgetragen

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
- Fallback-Matching über extrahierten Quellordnernamen hinzugefügt (matcht gegen Plugin-Slug und GitHub-Repo-Name aus der Download-URL)

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
