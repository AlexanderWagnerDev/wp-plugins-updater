=== AWDev Plugins Updater ===
Mitwirkende: alexanderwagnerdev
Schlagwörter: updater, self-hosted, plugin update, update manager
Erfordert mindestens: 6.3
Getestet bis: 6.9
Erfordert PHP: 7.4
Stabile Version: 0.1.2
Lizenz: GPLv2 or later
Lizenz-URI: https://www.gnu.org/licenses/gpl-2.0.html

Updater für AlexanderWagnerDev-Plugins. Kein WordPress.org erforderlich.

== Beschreibung ==
Der AWDev Plugins Updater ersetzt den WordPress.org-Update-Kanal für AlexanderWagnerDev-Plugins durch eine direkte Verbindung zum AWDev Update-Server. Damit hast du die volle Kontrolle über Versionierung, Verteilung und Veröffentlichungszeitpunkt — vollständig unabhängig von der WordPress.org-Review-Queue.

Der Updater klinkt sich direkt in das native WordPress-Update-System ein, sodass Updates wie jedes andere Plugin-Update im Standard-Plugins-Bildschirm erscheinen. Es werden keine Drittanbieter-Update-Bibliotheken verwendet.

Funktionen:
* Native WordPress-Update-Integration (keine Drittanbieter-Bibliotheken)
* Einstellungsseite unter Einstellungen → AWDev Plugins Updater
* Integrierte Unterstützung für DarkAdmin – Dark Mode for Adminpanel (automatisch registriert)
* Auto-Update-Toggle pro Plugin — wird sofort beim Klick gespeichert, kein Speichern-Button nötig
* Globaler Auto-Update-Hauptschalter — überträgt Zustand sofort auf alle Per-Plugin-Toggles
* Manueller Re-Check-Button pro Plugin — leert den Transient und ruft sofort die neueste Version ab
* Ein-Klick-Aktualisieren-Button — erscheint automatisch wenn eine neuere Remote-Version verfügbar ist
* Konfigurierbares Update-Cache-Intervall (1h–168h, Standard 6h)
* Manueller Cache-Flush-Button
* Unterstützung für das "Version Details"-Popup im WP-Update-Bildschirm
* Automatische Ordnernamens-Korrektur nach ZIP-Extraktion (inkl. Bulk-Updates und Zufalls-Suffix-Ordner)
* Vollständige Dark-Mode-Unterstützung — Einstellungsseite passt sich automatisch via DarkAdmin-CSS-Variablen an
* Übersetzungen: de_DE, de_AT, en_US
* Saubere Deinstallation — alle Plugin-Optionen werden beim Löschen aus der Datenbank entfernt

== Installation ==
1. Neueste awdev-plugin-updater.zip von der GitHub-Releases-Seite herunterladen.
2. Über Plugins → Neu hinzufügen → Plugin hochladen installieren oder in /wp-content/plugins/awdev-plugin-updater/ entpacken.
3. Plugin über den Plugins-Bildschirm in WordPress aktivieren.
4. Unter Einstellungen → AWDev Plugins Updater die verwalteten Plugins überprüfen.

== Häufig gestellte Fragen ==
= Muss ich etwas konfigurieren? =
Nein. DarkAdmin wird automatisch registriert, wenn es installiert ist. Die Standard-Server-URL ist vorkonfiguriert.

= Wie funktioniert der Auto-Update-Toggle? =
Jedes Plugin hat einen Auto-Update-Toggle auf der Einstellungsseite. Ein Klick speichert den Zustand sofort via AJAX — kein Speichern-Button nötig. Wenn aktiviert, installiert WordPress Updates automatisch im regelmäßigen Hintergrund-Update-Zyklus.

= Wie löse ich eine manuelle Update-Prüfung aus? =
Klicke auf den Re-Check-Button (Kreis-Pfeil-Symbol) neben einem Plugin auf der Einstellungsseite. Dadurch werden die gecachten Versionsdaten gelöscht und die neueste Version sofort vom Server abgerufen.

= Wie oft prüft WordPress auf Updates? =
WordPress prüft standardmäßig ungefähr alle 12 Stunden. Der Updater cached API-Antworten für das konfigurierte Intervall (Standard 6h). Verwende den "Update-Cache leeren"-Button für eine sofortige vollständige Neuprüfung.

= Ersetzt das WordPress.org-Updates? =
Nur für AlexanderWagnerDev-Plugins, die in diesem Updater registriert sind. Alle anderen Plugins werden weiterhin normal über WordPress.org aktualisiert.

= Ist das mit Dark Mode kompatibel? =
Ja. Die Einstellungsseite unterstützt DarkAdmin vollständig und passt sich automatisch an den Dark Mode an.

== Changelog ==
= 0.1.2 =
* Removed error logging calls
* Removed dead add-plugin JS code (no UI counterpart existed)

= 0.1.1 =
* fix_folder_name(): Absturz behoben wenn ZIP-Inhalt ohne Unterordner extrahiert wird (flache Struktur) — rename() wurde mit Zielpfad innerhalb des Quellverzeichnisses aufgerufen; Umbenennung wird jetzt in ein Geschwisterverzeichnis umgeleitet
* plugins_loaded-Hook-Priorität auf 20 erhöht für zuverlässige Initialisierungsreihenfolge
* rename_source() erhält jetzt den vollständig aufgelösten Zielpfad direkt
* awdev_get_local_version() cached get_plugins() via wp_cache_get/set (Gruppe awdev_updater) — höchstens ein Dateisystem-Scan pro Request

= 0.1.0 =
* awdev_fetch_api_data(): null/leerer API-Body wird jetzt explizit als Fehler behandelt und als false gecacht
* saveToggle() in settings.js: fehlende Fehlerbehandlung behoben — Toggle wird bei Fehler visuell zurückgesetzt
* Re-Check-Button: Versions-Zelle wird vor dem Fetch auf ... zurückgesetzt und zeigt ? bei Fehler
* compareVersions(): Number() durch parseInt() ersetzt für sichere Behandlung von Pre-Release-Suffixen
* awdev_sync_auto_update_defaults() hinzugefügt — neue Built-in-Defaults ohne Deaktivieren/Aktivieren
* escHtml() in settings.js escapt jetzt auch Apostrophe
* Ungenutzte jQuery-Abhängigkeit aus wp_enqueue_script() entfernt
* Ebenfalls enthalten (von 0.0.9): json_last_error()-Validierung, error_log() bei Rename-Fehler, Tages-Anzeige, Autor-Feld aus API, uninstall.php, awdev_built_in_plugins(), generate-l10n.yml, update_option()-Refactoring

= 0.0.8 =
* Auto-Update-Filter gab null statt true für AWDev-Plugins zurück — WP-Hintergrund-Updates wurden dadurch lautlos übersprungen (behoben)
* Ordnerumbenennung nach ZIP-Extraktion schlug lautlos fehl wenn Zielordner bereits existierte (behoben)

= 0.0.7 =
* DarkAdmin-Kompatibilität auf der Einstellungsseite verbessert
* Farbbezogene !important-Deklarationen aus Basis-Selektoren entfernt
* DarkAdmin-spezifische Override-Regeln beibehalten

= 0.0.6 =
* Sprachdatei-msgid-Strings an __()-Aufrufe in settings.php angepasst
* Zwei nicht übereinstimmende Strings korrigiert
* Project-Id-Version in allen .po- und .pot-Dateien auf 0.0.6 aktualisiert

= 0.0.5 =
* Fehler bei Ordnerumbenennung bei Bulk-Updates und WP Auto-Updates behoben
* Fallback-Matching über extrahierten Quellordnernamen hinzugefügt
* Rename-Logik in private rename_source()-Methode ausgelagert

= 0.0.4 =
* wp_safe_redirect() verwendet
* wp_unslash() und absint() für POST-Input hinzugefügt
* translators-Kommentare hinzugefügt
* rename() durch WP_Filesystem::move() ersetzt
* phpcs:ignore für direkte DB-Queries hinzugefügt

= 0.0.3 =
* Verschachtelte Formular-Elemente behoben
* Per-Plugin-Toggles speichern sofort via AJAX
* Globaler Toggle spiegelt Zustand sofort
* Debug-Ausgaben entfernt

= 0.0.2 =
* Auto-Update-Toggle pro Plugin hinzugefügt
* Manuellen Re-Check-Button hinzugefügt
* Ein-Klick-Aktualisieren-Button hinzugefügt
* Versionserkennung korrigiert
* Transient-Key-Fehler behoben
* Dark-Mode-Kompatibilität behoben

= 0.0.1 =
* Erste Veröffentlichung
* Native WordPress-Update-Hook-Integration
* Einstellungsseite mit verwalteter Plugin-Tabelle und Cache-Flush
* Integrierte DarkAdmin-Unterstützung
* Übersetzungen: de_DE, de_AT, en_US
