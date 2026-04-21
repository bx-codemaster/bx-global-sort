<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Global Sort - German System Module Texts
 * 
 * System module configuration texts for BX Global Sort.
 * Module description, title, description and status constants.
 * 
 * @package    BX Global Sort
 * @subpackage Language
 * @category   System Module
 * @author     Axel Benkert
 * @version    1.2
 * @since      1.0.0
 * @date       2025-11-09
 * @copyright  2020-2025 Axel Benkert
 * @license    GNU General Public License
 */

  define('MODULE_BX_GLOBAL_SORT_TITLE', 'BX Global Sort');

  $description = '<h3 style="margin-top:0; display:flex; align-items:center; gap:8px;">' . xtc_image(DIR_WS_ICONS.'heading/bx_global_sort.png', 'BX Global Sort', '', '', 'style="max-height: 32px;"') . ' BX Global Sort</h3><p>ist ein professionelles Verwaltungstool für Modified eCommerce Shopsofware, das die Sortierung von Produkten auf der Startseite und in Kategorien vereinfacht. Mit einer modernen Drag & Drop Oberfläche können Sie Produkte intuitiv per Maus verschieben – die Sortierung wird automatisch gespeichert.</p>';
  if (basename($_SERVER['PHP_SELF']) == 'module_export.php') { 
    $description .= '<p><a class="button btnbox but_red" style="text-align:center;" onclick="return confirmLink(\'Alle Dateien löschen?\', \'\' ,this);" href="'.xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=bx_global_sort&action=custom').'">Alle Moduldateien löschen</a></p>';
  }
  define('MODULE_BX_GLOBAL_SORT_DESC', $description);

  define('MODULE_BX_GLOBAL_SORT_STATUS_TITLE', 'Modul aktiv?');
  define('MODULE_BX_GLOBAL_SORT_STATUS_DESC', 'Soll das Modul angezeigt werden?');
  
  // Custom Deinstallation Messages
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_DELETED', 'Erfolgreich gelöscht:');
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_FAILED', 'Fehler beim Löschen (bitte manuell per FTP entfernen):');
  define('MODULE_BX_GLOBAL_SORT_TEXT_SUCCESSFULLY_REMOVED', 'BX Global Sort wurde vollständig entfernt!');
  define('MODULE_BX_GLOBAL_SORT_TEXT_REMOVAL_INCOMPLETE', 'BX Global Sort wurde teilweise entfernt. Bitte prüfen Sie die Fehlermeldungen und löschen Sie die verbliebenen Dateien manuell per FTP.');
