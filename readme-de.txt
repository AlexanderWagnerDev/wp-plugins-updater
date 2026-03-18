=== AWDev Plugins Updater ===
Mitwirkende: alexanderwagnerdev
Schlagwoerter: updater, self-hosted, plugin update, update manager
Erfordert mindestens: 6.3
Getestet bis: 6.9
Erfordert PHP: 7.4
Stabile Version: 0.1.2
Lizenz: GPLv2 or later
Lizenz-URI: https://www.gnu.org/licenses/gpl-2.0.html

Selbst gehosteter Update-Manager fuer AlexanderWagnerDev-Plugins. Kein WordPress.org erforderlich.

== Beschreibung ==
Der AWDev Plugins Updater ersetzt den WordPress.org-Update-Kanal fuer AlexanderWagnerDev-Plugins durch einen selbst gehosteten Update-Server. Damit hast du die volle Kontrolle ueber Versionierung, Verteilung und Veroeffentlichungszeitpunkt — vollstaendig unabhaengig von der WordPress.org-Review-Queue.

Der Updater klinkt sich direkt in das native WordPress-Update-System ein, sodass Updates wie jedes andere Plugin-Update im Standard-Plugins-Bildschirm erscheinen. Es werden keine Drittanbieter-Update-Bibliotheken verwendet.

Funktionen:
* Native WordPress-Update-Integration (keine Drittanbieter-Bibliotheken)
* Einstellungsseite unter Einstellungen → AWDev Plugins Updater
* Integrierte Unterstuetzung fuer DarkAdmin – Dark Mode for Adminpanel (automatisch registriert)
* Auto-Update-Toggle pro Plugin — wird sofort beim Klick gespeichert, kein Speichern-Button noetig
* Globaler Auto-Update-Hauptschalter — uebertraegt Zustand sofort auf alle Per-Plugin-Toggles
* Manueller Re-Check-Button pro Plugin — leert den Transient und ruft sofort die neueste Version ab
* Ein-Klick-Aktualisieren-Button — erscheint automatisch wenn eine neuere Remote-Version verfuegbar ist
* Weitere Plugins ueber die Einstellungsseite hinzufuegen — keine Codeaenderungen noetig
* Konfigurierbares Update-Cache-Intervall (1h–168h, Standard 6h)
* Manueller Cache-Flush-Button
* Unterstuetzung fuer das "Version Details"-Popup im WP-Update-Bildschirm
* Automatische Ordnernamens-Korrektur nach ZIP-Extraktion (inkl. Bulk-Updates und Zufalls-Suffix-Ordner)
* Vollstaendige Dark-Mode-Unterstuetzung — Einstellungsseite passt sich automatisch via DarkAdmin-CSS-Variablen an
* Uebersetzungen: de_DE, de_AT, en_US
* Saubere Deinstallation — alle Plugin-Optionen werden beim Loeschen aus der Datenbank entfernt

== Installation ==
1. Neueste awdev-plugin-updater.zip von der GitHub-Releases-Seite herunterladen.
2. Ueber Plugins → Neu hinzufuegen → Plugin hochladen installieren oder in /wp-content/plugins/awdev-plugin-updater/ entpacken.
3. Plugin ueber den Plugins-Bildschirm in WordPress aktivieren.
4. Unter Einstellungen → AWDev Plugins Updater die verwalteten Plugins ueberpruefen.

== Haeufig gestellte Fragen ==
= Muss ich etwas konfigurieren? =
Nein. DarkAdmin wird automatisch registriert, wenn es installiert ist. Die Standard-Server-URL ist vorkonfiguriert.

= Kann ich weitere Plugins hinzufuegen? =
Ja. Verwende die Einstellungsseite, um weitere AlexanderWagnerDev-Plugins ueber ihren Basename (z. B. my-plugin/my-plugin.php) und API-Slug hinzuzufuegen.

= Wie funktioniert der Auto-Update-Toggle? =
Jedes Plugin hat einen Auto-Update-Toggle auf der Einstellungsseite. Ein Klick speichert den Zustand sofort via AJAX — kein Speichern-Button noetig. Wenn aktiviert, installiert WordPress Updates automatisch im regelmaessigen Hintergrund-Update-Zyklus.

= Wie loese ich eine manuelle Update-Pruefung aus? =
Klicke auf den Re-Check-Button (Kreis-Pfeil-Symbol) neben einem Plugin auf der Einstellungsseite. Dadurch werden die gecachten Versionsdaten geloescht und die neueste Version sofort vom Server abgerufen.

= Wie oft prueft WordPress auf Updates? =
WordPress prueft standardmaessig ungefaehr alle 12 Stunden. Der Updater cached API-Antworten fuer das konfigurierte Intervall (Standard 6h). Verwende den "Update-Cache leeren"-Button fuer eine sofortige vollstaendige Neupruefung.

= Ersetzt das WordPress.org-Updates? =
Nur fuer AlexanderWagnerDev-Plugins, die in diesem Updater registriert sind. Alle anderen Plugins werden weiterhin normal ueber WordPress.org aktualisiert.

= Ist das mit Dark Mode kompatibel? =
Ja. Die Einstellungsseite unterstuetzt DarkAdmin vollstaendig und passt sich automatisch an den Dark Mode an.

== Changelog ==
= 0.1.2 =
* Error-Logging-Aufrufe entfernt

= 0.1.1 =
* fix_folder_name(): Absturz behoben wenn ZIP-Inhalt ohne Unterordner extrahiert wird (flache Struktur) — rename() wurde mit Zielpfad innerhalb des Quellverzeichnisses aufgerufen; Umbenennung wird jetzt in ein Geschwisterverzeichnis umgeleitet
* plugins_loaded-Hook-Prioritaet auf 20 erhoeht fuer zuverlaessige Initialisierungsreihenfolge
* rename_source() erhaelt jetzt den vollstaendig aufgeloesten Zielpfad direkt
* awdev_get_local_version() cached get_plugins() via wp_cache_get/set (Gruppe awdev_updater) — hoechstens ein Dateisystem-Scan pro Request

= 0.1.0 =
* awdev_fetch_api_data(): null/leerer API-Body wird jetzt explizit als Fehler behandelt und als false gecacht
* saveToggle() in settings.js: fehlende Fehlerbehandlung behoben — Toggle wird bei Fehler visuell zurueckgesetzt
* Re-Check-Button: Versions-Zelle wird vor dem Fetch auf ... zurueckgesetzt und zeigt ? bei Fehler
* compareVersions(): Number() durch parseInt() ersetzt fuer sichere Behandlung von Pre-Release-Suffixen
* awdev_sync_auto_update_defaults() hinzugefuegt — neue Built-in-Defaults ohne Deaktivieren/Aktivieren
* escHtml() in settings.js escapt jetzt auch Apostrophe
* Ungenutzte jQuery-Abhaengigkeit aus wp_enqueue_script() entfernt
* Ebenfalls enthalten (von 0.0.9): json_last_error()-Validierung, error_log() bei Rename-Fehler, Tages-Anzeige, Autor-Feld aus API, uninstall.php, awdev_built_in_plugins(), generate-l10n.yml, update_option()-Refactoring

= 0.0.8 =
* Auto-Update-Filter gab null statt true fuer AWDev-Plugins zurueck — WP-Hintergrund-Updates wurden dadurch lautlos uebersprungen (behoben)
* Ordnerumbenennung nach ZIP-Extraktion schlug lautlos fehl wenn Zielordner bereits existierte (behoben)

= 0.0.7 =
* DarkAdmin-Kompatibilitaet auf der Einstellungsseite verbessert
* Farbbezogene !important-Deklarationen aus Basis-Selektoren entfernt
* DarkAdmin-spezifische Override-Regeln beibehalten

= 0.0.6 =
* Sprachdatei-msgid-Strings an __()-Aufrufe in settings.php angepasst
* Zwei nicht uebereinstimmende Strings korrigiert
* Project-Id-Version in allen .po- und .pot-Dateien auf 0.0.6 aktualisiert

= 0.0.5 =
* Fehler bei Ordnerumbenennung bei Bulk-Updates und WP Auto-Updates behoben
* Fallback-Matching ueber extrahierten Quellordnernamen hinzugefuegt
* Rename-Logik in private rename_source()-Methode ausgelagert

= 0.0.4 =
* wp_redirect() durch wp_safe_redirect() ersetzt
* wp_unslash() und absint() fuer POST-Input hinzugefuegt
* translators-Kommentare hinzugefuegt
* rename() durch WP_Filesystem::move() ersetzt
* phpcs:ignore fuer direkte DB-Queries hinzugefuegt

= 0.0.3 =
* Verschachtelte Formular-Elemente behoben
* Per-Plugin-Toggles speichern sofort via AJAX
* Globaler Toggle spiegelt Zustand sofort
* Debug-Ausgaben entfernt

= 0.0.2 =
* Auto-Update-Toggle pro Plugin hinzugefuegt
* Manuellen Re-Check-Button hinzugefuegt
* Ein-Klick-Aktualisieren-Button hinzugefuegt
* Versionserkennung korrigiert
* Transient-Key-Fehler behoben
* Dark-Mode-Kompatibilitaet behoben

= 0.0.1 =
* Erste Veroeffentlichung
* Native WordPress-Update-Hook-Integration
* Einstellungsseite mit verwalteter Plugin-Tabelle und Cache-Flush
* Integrierte DarkAdmin-Unterstuetzung
* Uebersetzungen: de_DE, de_AT, en_US
