<?php


  define('MODULE_BX_GLOBAL_SORT_TITLE', 'BX Global Sort');

  $description = '<h3 style="margin-top: 0;">🍋 BX Global Sort</h3><p>ist ein professionelles Verwaltungstool für Modified eCommerce Shopsofware, das die Sortierung von Produkten auf der Startseite und in Kategorien vereinfacht. Mit einer modernen Drag & Drop Oberfläche können Sie Produkte intuitiv per Maus verschieben – die Sortierung wird automatisch gespeichert.</p>';
  if (basename($_SERVER['PHP_SELF']) == 'module_export.php') { 
    $description .= '<p><a class="button btnbox but_red" style="text-align:center;" onclick="return confirmLink(\'Alle Dateien löschen?\', \'\' ,this);" href="'.xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=bx_global_sort&action=custom').'">Alle Moduldateien löschen</a></p>';
  }
  define('MODULE_BX_IMPORT_EXPORT_LONG_DESCRIPTION', $description);
  define('MODULE_BX_GLOBAL_SORT_STATUS_TITLE', 'Modul aktiv?');
  define('MODULE_BX_GLOBAL_SORT_STATUS_DESC', 'Soll das Modul angezeigt werden?');
  
  // Custom Deinstallation Messages
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_DELETED', 'Erfolgreich gelöscht:');
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_FAILED', 'Fehler beim Löschen (bitte manuell per FTP entfernen):');
  define('MODULE_BX_GLOBAL_SORT_TEXT_SUCCESSFULLY_REMOVED', 'BX Global Sort wurde vollständig entfernt!');
  define('MODULE_BX_GLOBAL_SORT_TEXT_REMOVAL_INCOMPLETE', 'BX Global Sort wurde teilweise entfernt. Bitte prüfen Sie die Fehlermeldungen und löschen Sie die verbliebenen Dateien manuell per FTP.');
