# Mbusino-Parser (Bibliothek „Mbusino“)

Symcon-Modul, das das JSON eines MBusino (M-Bus-Auslesung, z. B. Wärme- oder Energiezähler) aus einer String-Variable in einzelne Variablen zerlegt. Öffentliches Repo `da8ter/Mbusino-Parser`, Branch `main`.

Projektwissen und Abweichungen vom Hausstandard: **`docs/stand.md`** (Einstieg `docs/README.md`). Betriebsdaten dieses Rechners: `CLAUDE.local.md` (nicht eingecheckt) — dort steht auch, dass die Arbeitskopie hinter GitHub liegt.

## Aufbau

- **`Mbusino/`** (Gerät, Klasse `MBusinoParser`, Präfix `MBUSINO`): `module.php` (130 Zeilen), `form.json` (Auswahl der JSON-Variable, Knopf „Jetzt ausführen“ → `RequestAction('ParseNow')`), `locale.json`.
- Öffentliche Funktion: `MBUSINO_ParseNow($id)`.

## Prüfen

Kein Prüfstand. `php -l Mbusino/module.php`; Verhalten mit einer Test-Variable und Beispiel-JSON in Symcon prüfen.

## Regeln

- **Commits:** ein Thema je Commit, deutsche Botschaft, **ohne** Co-Authored-By-Zeile; Prüfungen vorher.
- **Nie** `git checkout`/`git restore` auf Dateien: Arbeitskopien können nicht committete Arbeit enthalten.
- **Vor jeder Änderung** den Stand von GitHub holen (die letzten Änderungen entstanden in der Weboberfläche); Push nur auf Zuruf.
- **Release:** `version`, `build` und `date` (Unix-Zeitstempel) in `library.json` hochsetzen.
- **Öffentliches Repo:** keine IP-Adressen, Ports, Instanz-IDs, Zähler-Seriennummern (`fab_number`), Token, Pfade unter `/Users/` — auch nicht in Beispiel-JSON.
- **Neuer Code** folgt den Hausregeln (Module Strict, Darstellungen statt Profile, `Translate`); ein Umbau des Bestands nur auf Zuruf (Bestandsvariablen behalten Ident und Historie).
- **Symcon-Plattformwissen** (gemessen, für alle Module): https://github.com/da8ter/SymDo-Family-Organizer/tree/SymDo-Beta/docs/plattform, lokal `../List/docs/plattform/`.
