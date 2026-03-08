<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Global Sort - Deutsche Sprachtexte
 * 
 * Interface-Texte für das BX Global Sort Modul (Admin-Bereich).
 * Alle UI-Elemente, Buttons und Beschreibungen in deutscher Sprache.
 * 
 * @package    BX Global Sort
 * @subpackage Language
 * @category   Admin
 * @author     Axel Benkert
 * @version    1.2
 * @since      1.0.0
 * @date       2025-11-09
 * @copyright  2020-2025 Axel Benkert
 * @license    GNU General Public License
 */

  define('HEADING_BX_GLOBAL_SORT_TITLE', 'BX Global Sort');
  define('HEADING_BX_GLOBAL_SORT_SUB_TITLE', 'Zentrale Produktsortierung für die Startseite und den Kategorien');
  define('BX_GS_TXT_STARTPAGE', 'Startseite');
  define('BX_GS_TXT_SAVE', 'Speichern');
  define('BX_GS_TXT_IMAGE', 'Bild');
  define('BX_GS_TXT_ART_NO', 'Art.-Nr.');
  define('BX_GS_TXT_NAME', 'Bezeichnung');
  define('BX_GS_TXT_CATEGORY', 'Kategorie');
  define('BX_GS_TXT_STATUS', 'Status');
  define('BX_GS_TXT_CURRENT', 'Aktuell');
  define('BX_GS_TXT_TOP', 'Top');
  define('BX_GS_TXT_NEW', 'Neu');  
  define('BX_GS_TXT_SAVED_SUCCESSFULLY', 'Die neue Sortierung wurde erfolgreich abgespeichert!');
  define('BX_GS_TXT_MOVE_TOP', 'Ganz nach oben');
  define('BX_GS_TXT_MOVE_BOTTOM', 'Ganz nach unten');
  define('BX_GS_TXT_MOVE_UP', 'Eins nach oben');
  define('BX_GS_TXT_MOVE_DOWN', 'Eins nach unten');
  define('BX_GS_TXT_CHOOSE_CATEGORY', 'Bitte Kategorie wählen.');
  define('BX_GS_TXT_CATEGORIES', 'Kategorien');
  
  // AJAX Toast-Notifications
  define('BX_GS_AJAX_ERROR_NO_PRODUCT_ID', 'Fehler: Produkt-ID nicht gefunden');
  define('BX_GS_AJAX_ERROR_INVALID_PRODUCT_ID', 'Fehler: Ungültige Produkt-ID');
  define('BX_GS_AJAX_SUCCESS_SAVED', 'Sortierung gespeichert');
  define('BX_GS_AJAX_ERROR_SAVING', 'Fehler beim Speichern');
  define('BX_GS_AJAX_ERROR_SERVER', 'Server-Fehler');
  
  // AJAX Backend Response Messages
  define('BX_GS_AJAX_SUCCESS_STARTPAGE', 'Startseiten-Sortierung gespeichert');
  define('BX_GS_AJAX_SUCCESS_CATEGORY', 'Kategorie-Sortierung gespeichert');
  define('BX_GS_AJAX_ERROR_DATABASE', 'Datenbankfehler beim Speichern');
  define('BX_GS_AJAX_ERROR_INVALID_INPUT', 'Ungültige Eingabedaten');
  
  // Translation Validation Error Messages
  define('BX_GS_TRANSLATION_ERROR_TITLE', 'Fehlende Übersetzungen gefunden');
  define('BX_GS_TRANSLATION_ERROR_MESSAGE', 'Die folgenden Kategorien und/oder Produkte haben keine Übersetzung in der Sprache %s. Bitte ergänzen Sie die fehlenden Übersetzungen, bevor Sie diese Seite verwenden können.');
  define('BX_GS_MISSING_CATEGORIES', 'Kategorien ohne Übersetzung');
  define('BX_GS_MISSING_PRODUCTS', 'Produkte ohne Übersetzung');
  define('BX_GS_EDIT_TRANSLATION', 'Bearbeiten');
  define('BX_GS_WHAT_TO_DO', 'Was ist zu tun?');
  define('BX_GS_INSTRUCTION_1', 'Klicken Sie auf "Bearbeiten" neben jedem Eintrag, um die fehlende Übersetzung hinzuzufügen');
  define('BX_GS_INSTRUCTION_2', 'Füllen Sie das Feld "Kategoriename" bzw. "Produktname" in der entsprechenden Sprache aus');
  define('BX_GS_INSTRUCTION_3', 'Klicken Sie nach dem Speichern auf "Seite neu laden" um fortzufahren');
  define('BX_GS_RELOAD_PAGE', 'Seite neu laden');
  
  // Error Messages
  define('BX_GS_NO_CATEGORY_PATH', 'Keine Kategorie-Zuordnung');
  