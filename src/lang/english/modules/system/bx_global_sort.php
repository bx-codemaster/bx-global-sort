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

  define('MODULE_BX_GLOBAL_SORT_TITLE', 'BX Global Sort');

  $description = '
<details class="bxac-card">
  <summary class="bxac-summary" style="list-style: none;">
  <span class="bxac-arrow">▸</span>
  <span class="bxac-title">' . xtc_image(DIR_WS_ICONS.'heading/bx_global_sort.png', 'BX Global Sort', '', '', 'style="max-height: 32px; vertical-align: middle; margin-right: 8px;"') . 'BX Global Sort</span>
  </summary>
  <div class="bxac-body">
    <h3 style="margin-top: 0;">Professional Management Tool</h3>
    <p>A tool for Modified eCommerce Shopsoftware that simplifies the sorting of products on the home page and in categories. With a modern drag & drop interface, you can intuitively move products with your mouse – the sorting is saved automatically.</p>';

  if (basename($_SERVER['PHP_SELF']) == 'module_export.php') { 
    $description .= '<p><a class="button btnbox but_red" style="text-align:center;" onclick="return confirmLink(\'Delete all files?\', \'\' ,this);" href="'.xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=bx_global_sort&action=custom').'">Delete all module files</a></p>';
  }
  $description .= '</div></details>';
  
  define('MODULE_BX_GLOBAL_SORT_DESC', $description);
  define('MODULE_BX_GLOBAL_SORT_STATUS_TITLE', 'Module active?');
  define('MODULE_BX_GLOBAL_SORT_STATUS_DESC', 'Should the module be displayed?');
  
  // Custom Deinstallation Messages
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_DELETED', 'Successfully deleted:');
  define('MODULE_BX_GLOBAL_SORT_TEXT_FILES_FAILED', 'Failed to delete (please remove manually via FTP):');
  define('MODULE_BX_GLOBAL_SORT_TEXT_SUCCESSFULLY_REMOVED', 'BX Global Sort has been completely removed!');
  define('MODULE_BX_GLOBAL_SORT_TEXT_REMOVAL_INCOMPLETE', 'BX Global Sort has been partially removed. Please check the error messages and delete the remaining files manually via FTP.');
