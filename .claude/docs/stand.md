# Stand

Das Modul entstand im März 2025 in einem Zug über die GitHub-Weboberfläche und ist seitdem unverändert. Hier steht, wo es vom heutigen Hausstandard abweicht, damit Änderungen das nicht unbemerkt fortschreiben.

## Funktionsweise (Kurzfassung)

Das Modul beobachtet eine String-Variable mit dem JSON eines MBusino (Liste von Einträgen mit `name`, `value_scaled`, `units`, optional `value_string`) per `VM_UPDATE` und legt je Eintrag eine Variable unter der Instanz an. Mehrfach vorkommende Namen werden durchnummeriert, Datumswerte (`YYYYMMDD`, `YYYYMMDDhhmm`) per `strtotime` zu Zeitstempeln. `time_point`, `on_time`, `model_version`, `fab_number` und `error_flags` werden Integer-, alle anderen Float-Variablen; geschrieben wird nur bei geändertem Wert.

## Abweichungen vom Hausstandard (Stand des Codes)

- Kein `declare(strict_types=1)`, Basisklasse `IPSModule`, untypisierte Signaturen.
- Variablen entstehen per `IPS_CreateVariable`/`IPS_SetIdent` statt `RegisterVariable*`/`MaintainVariable` und bekommen Systemprofile (`~Electricity.Wh`, `~Gas`, `~Temperature` …) statt Darstellungen.
- Deutsche Texte fest im Code (Variablennamen, Logmeldungen), kein `Translate`; Logs über `IPS_LogMessage`.
- Kein Kernel-Runlevel-Check in `ApplyChanges`; die Registrierung auf `VM_UPDATE` einer früher gewählten Variable wird beim Wechsel nicht aufgehoben.
- `module.json`: `vendor` und `url` leer; `library.json`: `url` leer, `build`/`date` 0. README-Texte sind Platzhalter der Vorlage.

Stand: geprüft gegen den Code am 08.10.2026
