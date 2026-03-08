<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Global Sort - English System Module Texts
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

  define('MODULE_BX_GLOBAL_SORT_TEXT_TITLE', 'BX Global Sort');
  $description = '<h3 style="margin-top: 0;">🍋 BX Global Sort</h3><p>is a professional management tool for Modified eCommerce Shopsofware that simplifies the sorting of products on the home page and in categories. With a modern drag & drop interface, you can intuitively move products with your mouse – the sorting is saved automatically.</p>';
  if (basename($_SERVER['PHP_SELF']) == 'module_export.php') { 
    $description .= '<p><a class="button btnbox but_red" style="text-align:center;" onclick="return confirmLink(\'Delete all files?\', \'\' ,this);" href="'.xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=bx_global_sort&action=custom').'">Delete all module files</a></p>';
  }
  define('MODULE_BX_GLOBAL_SORT_TEXT_DESCRIPTION', $description);
  define('MODULE_BX_GLOBAL_SORT_STATUS_TITLE', 'Module active?');
  define('MODULE_BX_GLOBAL_SORT_STATUS_DESC', 'Should the module be displayed?');
  
  // Custom Deinstallation Messages
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_DELETED', 'Successfully deleted:');
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_FAILED', 'Failed to delete (please remove manually via FTP):');
  define('MODULE_BX_GLOBAL_SORT_TEXT_SUCCESSFULLY_REMOVED', 'BX Global Sort has been completely removed!');
  define('MODULE_BX_GLOBAL_SORT_TEXT_REMOVAL_INCOMPLETE', 'BX Global Sort has been partially removed. Please check the error messages and delete the remaining files manually via FTP.');
