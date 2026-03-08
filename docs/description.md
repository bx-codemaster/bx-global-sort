# BX Global Sort

## Kurzbeschreibung
BX Global Sort ist ein Produkt-Sortierungs-Manager für modified eCommerce. Es ermöglicht die zentrale Verwaltung der Produktsortierung für die Startseite und Kategorien mit einer modernen Drag & Drop Oberfläche und automatischer AJAX-Speicherung.

Die Datei beschreibt das Modul als Produktbestandteil. Konkrete Bedienabläufe stehen in `docs/usage.md`.

## Ziel und Nutzen
- Einfache und intuitive Sortierung von Produkten auf der Startseite per Drag & Drop
- Sortierung von Produkten innerhalb von Kategorien
- Automatisches Speichern ohne manuelle Save-Buttons
- Visuelles Feedback mit Toast-Notifications
- Vollständige Internationalisierung (Deutsch/Englisch)

## Funktionsumfang auf Produktebene
- Startseiten-Sortierung: Produkte auf der Startseite per Drag & Drop sortieren
- Kategorie-Sortierung: Produkte innerhalb von Kategorien sortieren
- Status-Anzeige: Produkt-Status und Top-Produkte auf einen Blick
- AJAX Auto-Save: Automatisches Speichern bei Drag & Drop
- Toast-Notifications: Visuelles Feedback bei Erfolg/Fehler mit Slide-Animation
- Fallback: Manuelle Speicherung via Form-Submit bei deaktiviertem JavaScript
- Kompakte 7-Spalten-Tabelle mit Badge-Styling für Kategorie-Pfade

## Technische Einordnung
- Integration in modified über Admin-Menü und Sprachdateien
- Verwendet jQuery 1.8.3 (Modified eCommerce legacy compatibility) und jQuery UI 1.12.1 Sortable
- CSRF Token Protection für sichere AJAX-Authentifizierung
- PHP 7.4+ mit filter_input(), Type Hinting und Null Coalescing
- Vollständige Internationalisierung mit 39 Sprachkonstanten (DE/EN)

## Paketstruktur
```text
src/
  admin/
    bx_global_sort.php
    includes/
      classes/
        ...
  lang/
    german/
    english/
```

## Kompatibilität und Abhängigkeiten
- modified eCommerce: `3.3.0`
- PHP: `^7.4 || ^8.0`

## Sicherheit und Betrieb
- Einsatz nur im administrativen Kontext mit passenden Rechten
- CSRF Token validation für alle AJAX-Requests
- Input sanitization mit filter_input() und filter_var()
- SQL injection prevention via Type Casting und xtc_db_input()
- XSS protection mit htmlspecialchars() für User Data Output
- HTTP status codes (400, 500) für proper error handling
