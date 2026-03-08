# BX Global Sort - Bedienung

## Zweck dieses Dokuments
Diese Datei beschreibt die praktische Handhabung des Moduls im Adminbereich.
Eine allgemeine Modulbeschreibung findest du in `docs/description.md`.

## Voraussetzungen vor der Nutzung
- Modul ist installiert: `Admin -> Hilfsprogramme -> BX Global Sort`
- Zugriff im Admin vorhanden
- JavaScript aktiviert für optimale Funktionalität (Fallback verfügbar)

## Schnellstart
1. Modul im Admin aufrufen.
2. Kategorie auswählen oder "Startseite" wählen.
3. Produkte per Drag & Drop in die gewünschte Reihenfolge ziehen.
4. Änderungen werden automatisch gespeichert (Toast-Notification erscheint).
5. Bei Bedarf Fallback-Speicherung verwenden, wenn JavaScript deaktiviert ist.

## Bedienung

### Startseiten-Sortierung
- Wähle im Dropdown "Startseite" aus.
- Die Tabelle zeigt alle Produkte, die auf der Startseite angezeigt werden.
- Sortiere die Produkte durch Ziehen und Ablegen in der ersten Spalte (Drag Handle).
- Änderungen werden automatisch gespeichert - keine Save-Buttons erforderlich.
- Toast-Notifications zeigen Erfolg oder Fehler an.

### Kategorie-Sortierung
- Wähle eine Kategorie aus dem Dropdown aus.
- Die Tabelle zeigt alle Produkte in dieser Kategorie.
- Sortiere die Produkte per Drag & Drop.
- Status-Spalte zeigt Produkt-Status (aktiv/inaktiv).
- Top-Produkt-Spalte zeigt, ob es ein Top-Produkt ist.
- Kategorie-Pfad wird als Badge angezeigt.

### Zusatzfunktionen
- **AJAX Auto-Save**: Änderungen werden sofort gespeichert, ohne Seite neu zu laden.
- **Toast-Notifications**: Visuelles Feedback mit Slide-Animation.
- **Preflight Translation Check**: Überprüfung fehlender Übersetzungen vor dem Laden.
- **Fallback-Speicherung**: Bei deaktiviertem JavaScript kann manuell gespeichert werden.
- **7-Spalten-Tabelle**: Kompakte Ansicht mit allen relevanten Informationen.

## Empfohlene Workflows

### Workflow A: Startseiten-Produkte sortieren
1. "Startseite" auswählen.
2. Produkte in gewünschte Reihenfolge ziehen.
3. Automatisches Speichern abwarten.
4. Toast-Notification bestätigen.

### Workflow B: Kategorie-Produkte sortieren
1. Gewünschte Kategorie auswählen.
2. Produkte sortieren.
3. Status und Top-Produkte prüfen.
4. Änderungen automatisch gespeichert.

## Betriebsregeln
- Bei großen Kategorien kann das Laden etwas dauern.
- JavaScript sollte aktiviert sein für beste Erfahrung.
- Bei Problemen Logs im Admin prüfen.
- Übersetzungen werden automatisch validiert.

## Fehlerbehebung
- **Keine Toast-Notifications**: JavaScript prüfen, Fallback verwenden.
- **Übersetzungsfehler**: Preflight-Check zeigt fehlende Übersetzungen an.
- **Speicherfehler**: CSRF-Token oder Netzwerkprobleme prüfen.
