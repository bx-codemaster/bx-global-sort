<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Global Sort - Admin Menu Integration
 * 
 * Registriert den Menüeintrag für BX Global Sort im Admin-Bereich.
 * Fügt das Modul in die Tools-Sektion des modified eCommerce Admin-Menüs ein.
 * 
 * Menu Configuration:
 * - Box: BOX_HEADING_TOOLS (Werkzeuge)
 * - Access Name: bx_global_sort
 * - Filename: bx_global_sort.php
 * - SSL: Required
 * - Status: Controlled by MODULE_BX_GLOBAL_SORT_STATUS
 * 
 * @package    BX Global Sort
 * @subpackage Configuration
 * @category   Admin
 * @author     Axel Benkert
 * @version    1.2
 * @since      1.0.0
 * @date       2025-11-09
 * @copyright  2020-2025 Axel Benkert
 * @license    GNU General Public License
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

if (defined("MODULE_BX_GLOBAL_SORT_STATUS") && 'true' === MODULE_BX_GLOBAL_SORT_STATUS) {
  switch ($_SESSION['language_code']) {
    case 'de':
      if (!defined('MENU_NAME_BX_GS')) define('MENU_NAME_BX_GS', 'BX Global Sort');
      break;
    default:
      if (!defined('MENU_NAME_BX_GS')) define('MENU_NAME_BX_GS', 'BX Global Sort');
      break;
  }

  // BOX_HEADING_TOOLS = Werkzeuge-Menü im Admin
  $add_contents[BOX_HEADING_TOOLS][] = array( 
    'admin_access_name' => 'bx_global_sort',
    'filename' => 'bx_global_sort.php',
    'boxname' => MENU_NAME_BX_GS,
    'parameters' => '',
    'ssl' => 'SSL'
  );
}
