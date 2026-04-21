<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Global Sort - CSS Stylesheet
 * 
 * Externe Stylesheet-Datei für BX Global Sort Modul.
 * Enthält Styles für:
 * - jQuery UI Sortable Drag & Drop Effekte (state-highlight Placeholder)
 * - Toast-Notifications (success/error mit Slide-Animations)
 * - Row-States (saving/saved/error) für visuelles Feedback
 * - Kategorie-Pfad Badge-Styling (kompakte Chips mit Hover-Effekt)
 * - FontAwesome 6.5.1 Icon-Integration
 * 
 * @package    BX Global Sort
 * @subpackage Stylesheet
 * @category   Admin
 * @author     Axel Benkert
 * @version    1.2
 * @since      1.0.0
 * @date       2025-11-09
 * @copyright  2020-2025 Axel Benkert
 * @license    GNU General Public License
 */

  defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
  if (defined('MODULE_BX_GLOBAL_SORT_STATUS') && MODULE_BX_GLOBAL_SORT_STATUS == 'True' && basename($_SERVER['PHP_SELF']) == 'bx_global_sort.php') {
?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
  .state-highlight td {
	height: 50px;
    background-color: rgba(255, 255, 204, 0.7);
    border: 1px solid rgba(255, 153, 51, 0.7);
  }

  .ui-sortable-handle {
    cursor: move;
  }

  .ui-sortable-helper {
    display: table !important;
    width: 100%;
    background: #F7F7F7;
    opacity: 0.8;
    table-layout: fixed;
  }
  
  /* Spaltenbreiten im Drag-Helper (7 Spalten) */
  .ui-sortable-helper td:nth-child(1) { width: 15%; } /* Bild */
  .ui-sortable-helper td:nth-child(2) { width: 10%; } /* Art.-Nr. */
  .ui-sortable-helper td:nth-child(3) { width: 30%; } /* Name */
  .ui-sortable-helper td:nth-child(4) { width: 25%; } /* Kategorie */
  .ui-sortable-helper td:nth-child(5) { width: 5%; }  /* Top */
  .ui-sortable-helper td:nth-child(6) { width: 5%; }  /* Status */
  .ui-sortable-helper td:nth-child(7) { width: 10%; } /* Aktuell */

  a.button.move-to-top,
  a.button.move-up,
  a.button.move-down,
  a.button.move-to-bottom {
    margin: 0;
    line-height: 10px;
    padding: 6px 10px;
    width: 14px;
  }
  a.button.move-to-top {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
  }
  
  a.button.move-to-bottom {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
  }
  a.button.move-up {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
  a.button.move-down {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-left-radius: 0;
  }
  
  /* Toast Notifications */
  .bx-toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    min-width: 300px;
    background: #fff;
    padding: 16px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 9999;
    animation: slideIn 0.3s ease-out;
  }
  
  .bx-toast.success {
    border-left: 4px solid #28a745;
  }
  
  .bx-toast.error {
    border-left: 4px solid #dc3545;
  }
  
  .bx-toast.warning {
    border-left: 4px solid #ffc107;
  }
  
  .bx-toast-icon {
    font-size: 20px;
  }
  
  .bx-toast.success .bx-toast-icon {
    color: #28a745;
  }
  
  .bx-toast.error .bx-toast-icon {
    color: #dc3545;
  }
  
  .bx-toast.warning .bx-toast-icon {
    color: #ffc107;
  }
  
  .bx-toast-message {
    flex: 1;
    font-size: 14px;
    color: #333;
  }
  
  .bx-toast-close {
    cursor: pointer;
    color: #999;
    font-size: 18px;
    line-height: 1;
    padding: 0 4px;
  }
  
  .bx-toast-close:hover {
    color: #333;
  }
  
  @keyframes slideIn {
    from {
      transform: translateX(400px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }
  
  .bx-toast.hiding {
    animation: slideOut 0.3s ease-in forwards;
  }
  
  /* Row Save Indicator */
  tr.saving {
    background-color: #fff3cd !important;
    transition: background-color 0.3s;
  }
  
  
  tr.saved {
    background-color: #d4edda !important;
    transition: background-color 0.3s;
  }
  
  tr.error {
    background-color: #f8d7da !important;
    transition: background-color 0.3s;
  }
  
  /* Category Path Styling */
  #sortable tbody td ul,
  #sortable2 tbody td ul {
    margin: 0;
    padding: 0;
    list-style: none;
  }
  
  #sortable tbody td ul li,
  #sortable2 tbody td ul li {
    display: block;
    text-align: left;
    margin: 2px 0;
    padding: 0;
  }
  
  #sortable tbody td ul li a,
  #sortable2 tbody td ul li a {
    display: inline-block;
    padding: 2px 6px;
    margin: 1px 2px;
    font-size: 11px;
    line-height: 1.4;
    color: #AF417E;
    text-decoration: none;
    background-color: rgba(175, 65, 126, 0.1);
    border: 1px solid #aaaaaa;
    border-radius: 3px;
    transition: all 0.2s;
  }
  
  #sortable tbody td ul li a:hover,
  #sortable2 tbody td ul li a:hover {
    background-color: rgba(175, 65, 126, 0.3);
    border-color: #aaaaaa;
    color: #AF417E;
  }
  </style>
<?php
  }
?>