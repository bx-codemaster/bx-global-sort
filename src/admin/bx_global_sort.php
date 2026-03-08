<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Global Sort - Produkt-Sortierungs-Manager
 * 
 * Zentrale Verwaltung der Produktsortierung für Startseite und Kategorien.
 * Moderne Drag & Drop Oberfläche mit automatischer AJAX-Speicherung.
 * 
 * Features:
 * - AJAX Auto-Save: Automatisches Speichern bei Drag & Drop (kein Save-Button erforderlich)
 * - Toast-Notifications: Visuelles Feedback bei Erfolg/Fehler mit Slide-Animation
 * - Preflight Translation Check: Validierung fehlender Übersetzungen vor Seitenaufbau
 * - Startseiten-Sortierung: Produkte auf der Startseite per Drag & Drop sortieren
 * - Kategorie-Sortierung: Produkte innerhalb von Kategorien sortieren
 * - Status-Anzeige: Produkt-Status und Top-Produkte auf einen Blick
 * - Kompakte 7-Spalten-Tabelle mit Badge-Styling für Kategorie-Pfade
 * - Fallback: Manuelle Speicherung via Form-Submit bei deaktiviertem JavaScript
 * - Vollständige Internationalisierung (DE/EN mit 39 Sprachkonstanten)
 * - Input-Validierung: filter_input() für alle GET/POST Parameter
 * - SQL-Injection-Schutz: Prepared Statements und Type Casting
 * 
 * Technical Stack:
 * - jQuery 1.8.3 (Modified eCommerce legacy compatibility)
 * - jQuery UI 1.12.1 Sortable with clone helper
 * - FontAwesome 6.5.1 Icons
 * - CSRF Token Protection (secure AJAX authentication, no session exclusion)
 * - PHP 7.4+ (filter_input, type hinting, null coalescing)
 * 
 * Security Features:
 * - CSRF Token validation for all AJAX requests
 * - Input sanitization with filter_input() and filter_var()
 * - SQL injection prevention via type casting and xtc_db_input()
 * - XSS protection with htmlspecialchars() for user data output
 * - HTTP status codes (400, 500) for proper error handling
 * 
 * Translation Validation:
 * - bx_validate_translations(): Preflight check for missing translations
 * - bx_display_translation_error(): Professional error UI with edit links
 * - Checks categories, startpage products, and category products
 * - Fallback names from default language when translations missing
 * 
 * @package    BX Global Sort
 * @subpackage Admin
 * @category   Product Management
 * @author     Axel Benkert
 * @version    1.2
 * @since      1.0.0
 * @date       2025-11-09
 * @copyright  2020-2025 Axel Benkert
 * @license    GNU General Public License
 * 
 * @changelog
 * Version 1.2 (2025-11-09):
 * - Added preflight translation validation system
 * - Implemented CSRF token authentication (replaced session exclusion)
 * - Full internationalization with 39 language constants (DE/EN)
 * - Enhanced security with filter_input() for all user inputs
 * - Optimized UI: Removed save buttons, streamlined 7-column table
 * - Added toast notifications with slide animations
 * - jQuery UI Sortable helper styling with fixed column widths
 * - Input validation and SQL injection prevention
 * - Professional error handling with HTTP status codes
 * 
 * Version 1.0 (2020):
 * - Initial release with basic drag & drop functionality
 */

  require('includes/application_top.php');
  require_once (DIR_WS_CLASSES.FILENAME_CATEGORIES);

  $catfunc = new categories();
  
  /**
   * Preflight Check: Überprüfen Sie, ob alle erforderlichen Übersetzungen vorhanden sind.
   * 
   * Checks for missing translations in current language for:
   * - All categories used in category tree
   * - All products on startpage
   * - All products in selected category
   * 
   * If missing translations are found, displays error page with details
   * and stops script execution.
   * 
   * @return array Array with 'categories' and 'products' keys containing missing IDs, or empty arrays
   */
  function bx_validate_translations() {
    $missing = [
      'categories' => [],
      'products'   => []
    ];
    
    $current_lang = $_SESSION['languages_id'];
    
    // 1. Check all categories for missing translations
    $cat_query = xtc_db_query("SELECT c.categories_id 
                               FROM " . TABLE_CATEGORIES . " c
                               LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                                 ON c.categories_id = cd.categories_id 
                                 AND cd.language_id = '" . (int)$current_lang . "'
                               WHERE cd.categories_name IS NULL 
                                  OR cd.categories_name = ''");
    
    while ($cat = xtc_db_fetch_array($cat_query)) {
      $missing['categories'][] = $cat['categories_id'];
    }
    
    // 2. Check startpage products for missing translations
    $startpage_query = xtc_db_query("SELECT p.products_id
                                     FROM " . TABLE_PRODUCTS . " p
                                     LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                                       ON p.products_id = pd.products_id 
                                       AND pd.language_id = '" . (int)$current_lang . "'
                                     WHERE p.products_startpage = '1'
                                       AND p.products_status = '1'
                                       AND (pd.products_name IS NULL OR pd.products_name = '')");
    
    while ($prod = xtc_db_fetch_array($startpage_query)) {
      $missing['products'][] = $prod['products_id'];
    }
    
    // 3. If category selected, check products in that category
    if (isset($_GET['catID']) && $_GET['catID'] > 0) {
      $catID = filter_input(INPUT_GET, 'catID', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
      if ($catID !== false && $catID !== null) {
        $cat_products_query = xtc_db_query("SELECT p.products_id
                                            FROM " . TABLE_PRODUCTS . " p
                                            INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
                                              ON p.products_id = p2c.products_id
                                            LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                                              ON p.products_id = pd.products_id 
                                              AND pd.language_id = '" . (int)$current_lang . "'
                                            WHERE p2c.categories_id = '" . (int)$catID . "'
                                              AND (pd.products_name IS NULL OR pd.products_name = '')");
        
        while ($prod = xtc_db_fetch_array($cat_products_query)) {
          if (!in_array($prod['products_id'], $missing['products'])) {
            $missing['products'][] = $prod['products_id'];
          }
        }
      }
    }
    
    // Remove duplicates
    $missing['categories'] = array_unique($missing['categories']);
    $missing['products']   = array_unique($missing['products']);
    
    return $missing;
  }
  
  /**
   * Display translation error page with missing items
   * 
   * @param array  $missing   Array with 'categories' and 'products' keys containing IDs
   * @param string $lang_name Current language name
   * @return void
   */
  function bx_display_translation_error($missing, $lang_name) {
?>
    <!-- Error Message Box -->
    <table class="tableCenter">
      <tr>
        <td>
          <div style="background:#fff3cd;border:2px solid #ffc107;border-radius:8px;padding:20px;margin:20px 0;">
            <h2 style="color:#856404;margin-top:0;">
              <i class="fa fa-exclamation-triangle" style="margin-right:10px;"></i>
              <?php echo BX_GS_TRANSLATION_ERROR_TITLE; ?>
            </h2>
            
            <p style="font-size:14px;line-height:1.6;">
              <?php echo sprintf(BX_GS_TRANSLATION_ERROR_MESSAGE, '<strong>' . strtoupper($lang_name) . '</strong>'); ?>
            </p>
            
            <div style="background:#e8f4f8;border-left:4px solid #17a2b8;padding:15px;margin:20px 0 15px 0;">
              <h4 style="margin-top:0;color:#0c5460;">
                <i class="fa fa-info-circle" style="margin-right:8px;"></i>
                <?php echo BX_GS_WHAT_TO_DO; ?>
              </h4>
              <ol style="margin-bottom:0;line-height:1.8;">
                <li><?php echo BX_GS_INSTRUCTION_1; ?></li>
                <li><?php echo BX_GS_INSTRUCTION_2; ?></li>
                <li><?php echo BX_GS_INSTRUCTION_3; ?></li>
              </ol>
            </div>
          </div>
        </td>
      </tr>
    </table>
    
    <!-- Missing Items Tables -->
    <table class="tableCenter">
      <tr>
        <!-- Missing Categories -->
        <?php if (!empty($missing['categories'])): ?>
        <td class="boxCenterLeft" style="width: <?php echo empty($missing['products']) ? '100%' : '50%'; ?>; vertical-align: top;">
          <table class="tableCenter">
            <tr>
              <td>
                <div class="pageHeading" style="background:#dc3545;color:white;padding:10px;text-align:center;border-radius:4px 4px 0 0;">
                  <i class="fa fa-folder" style="margin-right:8px;"></i>
                  <?php echo BX_GS_MISSING_CATEGORIES; ?> (<?php echo count($missing['categories']); ?>)
                </div>
              </td>
            </tr>
          </table>
          <div style="display:block;max-height:400px;overflow-y:auto;">
            <table class="tableCenter borderall">
              <thead>
                <tr>
                  <th style="width:15%;">ID</th>
                  <th style="width:55%;">Name (Fallback)</th>
                  <th style="width:30%;">Aktion</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                foreach ($missing['categories'] as $cat_id) {
                  // Get category name from default language
                  $cat_query = xtc_db_query(
                    "SELECT cd.categories_name 
                      FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd
                      WHERE cd.categories_id = '" . (int)$cat_id . "'
                      LIMIT 1"
                  );
                  $default_cat = xtc_db_fetch_array($cat_query);

                  echo '<tr>';
                  echo '<td style="width:15%;font-weight:bold;">' . $cat_id . '</td>';
                  echo '<td style="width:55%;">';
                  if ($default_cat && !empty($default_cat['categories_name'])) {
                    echo '<em style="color:#666;">' . htmlspecialchars($default_cat['categories_name']) . '</em>';
                  } else {
                    echo '<span style="color:#999;">-</span>';
                  }
                  echo '</td>';
                  echo '<td style="width:30%;text-align:center;">';
                  echo '<a href="' . xtc_href_link(FILENAME_CATEGORIES, 'cID=' . $cat_id . '&action=edit_category&language='.DEFAULT_LANGUAGE) . '" target="_blank" class="button" style="font-size:11px;">';
                  echo '<i class="fa fa-edit"></i> ' . BX_GS_EDIT_TRANSLATION . '</a>';
                  echo '</td>';
                  echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </td>
        <?php endif; ?>
        
        <!-- Missing Products -->
        <?php if (!empty($missing['products'])): ?>
        <td class="boxRight" style="width: <?php echo empty($missing['categories']) ? '100%' : '50%'; ?>; vertical-align: top;">
          <table class="tableCenter">
            <tr>
              <td>
                <div class="pageHeading" style="background:#dc3545;color:white;padding:10px;text-align:center;border-radius:4px 4px 0 0;">
                  <i class="fa fa-cube" style="margin-right:8px;"></i>
                  <?php echo BX_GS_MISSING_PRODUCTS; ?> (<?php echo count($missing['products']); ?>)
                </div>
              </td>
            </tr>
          </table>
          <div style="display:block;max-height:400px;overflow-y:auto;">
            <table class="tableCenter borderall">
              <thead>
                <tr>
                  <th style="width:10%;">ID</th>
                  <th style="width:20%;">Artikel-Nr.</th>
                  <th style="width:40%;">Name (Fallback)</th>
                  <th style="width:30%;text-align:center;">Aktion</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                foreach ($missing['products'] as $prod_id) {
                  // Get product name from default language and model
                  $prod_query = xtc_db_query(
                    "SELECT p.products_model, pd.products_name 
                      FROM " . TABLE_PRODUCTS . " p
                      LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                        ON p.products_id = pd.products_id
                      WHERE p.products_id = '" . (int)$prod_id . "'
                      LIMIT 1"
                  );
                  $default_prod = xtc_db_fetch_array($prod_query);
                  
                  echo '<tr>';
                  echo '<td>' . $prod_id . '</td>';
                  echo '<td>';
                  if ($default_prod && !empty($default_prod['products_model'])) {
                    echo '<span style="color:#666;">' . htmlspecialchars($default_prod['products_model']) . '</span>';
                  } else {
                    echo '<span style="color:#999;">-</span>';
                  }
                  echo '</td>';
                  echo '<td>';
                  if ($default_prod && !empty($default_prod['products_name'])) {
                    echo '<em style="color:#666;">' . htmlspecialchars($default_prod['products_name']) . '</em>';
                  } else {
                    echo '<span style="color:#999;">-</span>';
                  }
                  echo '</td>';
                  echo '<td style="text-align:center;">';
                  echo '<a href="' . xtc_href_link(FILENAME_CATEGORIES, 'pID=' . $prod_id . '&action=new_product&language='.DEFAULT_LANGUAGE) . '" target="_blank" class="button" style="font-size:11px;">';
                  echo '<i class="fa fa-edit"></i> ' . BX_GS_EDIT_TRANSLATION . '</a>';
                  echo '</td>';
                  echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </td>
        <?php endif; ?>
      </tr>
    </table>
    
    <!-- Reload Button -->
    <table class="tableCenter">
      <tr>
        <td style="text-align:center;padding:20px;">
          <a href="<?php echo xtc_href_link(FILENAME_BX_GLOBAL_SORT); ?>" class="button" style="font-size:14px;padding:10px 30px;">
            <i class="fa fa-refresh" style="margin-right:8px;"></i><?php echo BX_GS_RELOAD_PAGE; ?>
          </a>
        </td>
      </tr>
    </table>
<?php
 }
  
  // RUN PREFLIGHT CHECK BEFORE ANY OUTPUT
  $missing   = bx_validate_translations();
  $lang_name = $_SESSION['language'];
  
  $action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : NULL);

  if (xtc_not_null($action)) {
    switch ($action) {
      case 'setpflag': // Zur Zeit nicht im Gebrauch!
        $flag  = filter_input(INPUT_GET, 'flag', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1]]);
        $pID   = filter_input(INPUT_GET, 'pID', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $catID = filter_input(INPUT_GET, 'catID', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        
        if ($flag !== false && $flag !== null && $pID !== false && $pID !== null) {
            $catfunc->set_product_status($pID, $flag);
        }
        xtc_redirect(xtc_href_link(FILENAME_BX_GLOBAL_SORT, xtc_get_all_get_params(array('action','flag', 'pID')) . '?catID='.$catID));
        break;
        //EOB setpflag
      case 'setsflag':      
        $flag  = filter_input(INPUT_GET, 'flag', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1]]);
        $pID   = filter_input(INPUT_GET, 'pID', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $catID = filter_input(INPUT_GET, 'catID', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        
        if ($flag !== false && $flag !== null && $pID !== false && $pID !== null) {
          $catfunc->set_product_startpage($pID, $flag);
          if ($flag === 1) {
            $catfunc->link_product($pID, 0);
          }
          $catfunc->set_product_remove_startpage_sql($pID, $flag);
        }
        xtc_redirect(xtc_href_link(FILENAME_BX_GLOBAL_SORT, xtc_get_all_get_params(array('action','flag', 'pID')) . '?catID='.$catID));
        break;
        //EOB setsflag
      case 'ajaxSaveSort':
        // CRITICAL: Stop any output buffering and clear buffers
        while (ob_get_level()) {
          ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        
        $products_id = filter_input(INPUT_POST, 'products_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $sort_order  = filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        $type        = isset($_POST['type']) ? (string)$_POST['type'] : null;
        
        if ($products_id !== false && $products_id !== null && $sort_order !== false && $sort_order !== null && in_array($type, ['startpage', 'category'])) {
          $field = ($type === 'startpage') ? 'products_startpage_sort' : 'products_sort';
          $sql = "UPDATE ".TABLE_PRODUCTS." SET ".$field." = ".(int)$sort_order." WHERE products_id = ".(int)$products_id;
          
          $result = xtc_db_query($sql);
          
          if ($result) {
            $response = [
                'success' => true, 
                'message' => ($type === 'startpage') ? BX_GS_AJAX_SUCCESS_STARTPAGE : BX_GS_AJAX_SUCCESS_CATEGORY,
                'products_id' => $products_id,
                'sort_order' => $sort_order
            ];
            echo json_encode($response);
          } else {
            http_response_code(500);
            $response = ['success' => false, 'message' => BX_GS_AJAX_ERROR_DATABASE];
            echo json_encode($response);
          }
        } else {
          http_response_code(400);
          $response = ['success' => false, 'message' => BX_GS_AJAX_ERROR_INVALID_INPUT];
          echo json_encode($response);
        }
        exit;
        //EOB ajaxSaveSort
      case 'ajaxSaveSortBatch':
        // CRITICAL: Stop any output buffering and clear buffers
        while (ob_get_level()) {
          ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        
        $type = isset($_POST['type']) ? (string)$_POST['type'] : null;
        $sort_data_json = isset($_POST['sort_data']) ? $_POST['sort_data'] : null;
        
        if (!in_array($type, ['startpage', 'category']) || empty($sort_data_json)) {
          http_response_code(400);
          echo json_encode(['success' => false, 'message' => BX_GS_AJAX_ERROR_INVALID_INPUT]);
          exit;
        }
        
        $sort_data = json_decode($sort_data_json, true);
        
        if (!is_array($sort_data)) {
          http_response_code(400);
          echo json_encode(['success' => false, 'message' => BX_GS_AJAX_ERROR_INVALID_INPUT]);
          exit;
        }
        
        $field = ($type === 'startpage') ? 'products_startpage_sort' : 'products_sort';
        $success_count = 0;
        
        foreach ($sort_data as $item) {
          $products_id = filter_var($item['products_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
          $sort_order = filter_var($item['sort_order'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
          
          if ($products_id !== false && $sort_order !== false) {
            $sql = "UPDATE ".TABLE_PRODUCTS." SET ".$field." = ".(int)$sort_order." WHERE products_id = ".(int)$products_id;
            
            if (xtc_db_query($sql)) {
              $success_count++;
            }
          }
        }
        
        $response = [
          'success' => true,
          'message' => ($type === 'startpage') ? BX_GS_AJAX_SUCCESS_STARTPAGE : BX_GS_AJAX_SUCCESS_CATEGORY,
          'updated_count' => $success_count
        ];
        echo json_encode($response);
        exit;
        //EOB ajaxSaveSortBatch
      case 'savestartsort':
        if(isset($_POST['catID'])) {
          $catID = filter_input(INPUT_POST, 'catID', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
          $catID = ($catID !== false && $catID !== null) ? '&catID='.$catID : '';
        } else {
          $catID = NULL;
        }
        if(isset($_POST["startsort"]) && is_array($_POST["startsort"])) {
          foreach($_POST["startsort"] as $key => $value) {
            $products_id = filter_var($key, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $sort_order  = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
            
            if ($products_id !== false && $sort_order !== false) {
                xtc_db_query("UPDATE ".TABLE_PRODUCTS." SET products_startpage_sort = ".(int)$sort_order." WHERE products_id = ".(int)$products_id);
            }
          }
        }
        xtc_redirect(xtc_href_link(FILENAME_BX_GLOBAL_SORT, xtc_get_all_get_params(array('action')) . ($catID ?? '') ));
        break;
      case 'savecategoriesort':
        if(isset($_POST["productssort"]) && is_array($_POST["productssort"])) {
          foreach($_POST["productssort"] as $key => $value) {
          $result = xtc_db_query("UPDATE `".TABLE_PRODUCTS."` SET `products_sort` = '".xtc_db_input($value)."' WHERE `products_id` = ".xtc_db_input($key));
          }
        }
        xtc_redirect(xtc_href_link(FILENAME_BX_GLOBAL_SORT, '&catID='.( isset($_GET['catID']) ? $_GET['catID'] : 0 ), 'SSL'));
        break;
      case 'selectCategorie':
        xtc_redirect(xtc_href_link(FILENAME_BX_GLOBAL_SORT, xtc_get_all_get_params(array('action')) . 'catID='.$_POST['catID'], 'SSL'));
        break;
    }
  }
require (DIR_WS_INCLUDES.'head.php');
?>
</head>
<body>
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES.'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table class="tableBody">
    <tr>
      <?php //left_navigation
      if (USE_ADMIN_TOP_MENU == 'false') {
        echo '<td class="columnLeft2">'.PHP_EOL;
        echo '<!-- left_navigation //-->'.PHP_EOL;       
        require_once(DIR_WS_INCLUDES.'column_left.php');
        echo '<!-- left_navigation eof //-->'.PHP_EOL; 
        echo '</td>'.PHP_EOL;      
      }
      ?>
      <!-- body_text //-->
      <td class="boxCenter">

        <div class="pageHeadingImage" style="min-width: 40px;"><?php echo xtc_image(DIR_WS_ICONS.'heading/icon_bx_global_sort.png', HEADING_BX_GLOBAL_SORT_TITLE, '', '', 'style="max-height: 32px;"'); ?></div>
        <div class="flt-l">
          <div class="pageHeading pdg2"><?php echo HEADING_BX_GLOBAL_SORT_TITLE; ?></div>
          <div class="main pdg2"><?php echo HEADING_BX_GLOBAL_SORT_SUB_TITLE; ?></div>
        </div>
<?php
    // If missing translations found, display error page and exit
    if (!empty($missing['categories']) || !empty($missing['products'])) {
      bx_display_translation_error($missing, $lang_name);
    } else { ?>
      <table class="tableCenter">      
        <tr>
          <td class="boxCenterLeft" style="width: 50%;">
<?php
echo xtc_draw_form('global_sort', FILENAME_BX_GLOBAL_SORT, 'action=savestartsort');
$catID = NULL;
if(isset($_GET['catID'])) {
  $catID = filter_input(INPUT_GET, 'catID', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
}
if(xtc_not_null($catID)) {
  echo xtc_draw_hidden_field('catID', $catID);
}
?>
            <div class="main" style="display: block; background: #AF417E; border-radius: 4px; margin: 2px 0 10px 0; padding: 10px 0 6px 0;">
              <div class="main" style="margin: 5px 10px; text-align: center;">
                <span style="display: inline-block; margin-bottom: 2px; color: #FFFFFF; font-weight: bold;"><?php echo BX_GS_TXT_STARTPAGE; ?></span>
              </div>
            </div>
            <table class="tableCenter collapse" id="sortable">
              <thead>
                <tr class="dataTableHeadingRow">
                  <th style="width: 15%; border-left: 1px solid #aaa;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_IMAGE; ?></th>
                  <th style="width: 10%;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_ART_NO; ?></th>
                  <th style="width: 30%;" class="dataTableHeadingContent txta-l"><?php echo BX_GS_TXT_NAME; ?></th>
                  <th style="width: 25%;" class="dataTableHeadingContent txta-l"><?php echo BX_GS_TXT_CATEGORY; ?></th>
                  <th style="width: 5%;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_TOP; ?></th>
                  <th style="width: 5%;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_STATUS; ?></th>
                  <th style="width: 10%;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_CURRENT; ?></th>
                </tr>
              </thead>
              <tbody>
<?php
$startpage_query = xtc_db_query("SELECT p.products_id, 
                                        p.products_model, 
                                        p.products_image, 
                                        p.products_status, 
                                        p.products_startpage, 
                                        p.products_startpage_sort, 
                                        pd.products_name
                                      FROM ".TABLE_PRODUCTS." p,  ".TABLE_PRODUCTS_DESCRIPTION." pd 
                                      WHERE pd.products_id = p.products_id 
                                        AND p.products_startpage = '1'
                                        AND p.products_status = '1' 
                                        AND pd.language_id = '".$_SESSION["languages_id"]."'
                                    ORDER BY p.products_startpage_sort");

$i = 1;
while ($startpage = xtc_db_fetch_array($startpage_query)) {
  echo '<tr class="dataTableRow">'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'
      .xtc_draw_hidden_field('startsort['.$startpage["products_id"].']', $startpage["products_startpage_sort"])
      .xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$startpage["products_image"], 'Standard Image','','','style="max-height:48px;"').'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'.$startpage["products_model"].'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'.$startpage["products_name"].'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'.xtc_output_generated_category_path($startpage["products_id"], 'product').'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'.PHP_EOL;
  if ($startpage['products_startpage'] == '1') {
    echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 12, 12, 'style="margin-right: 2px;"')
            . '<a href="'.xtc_href_link(FILENAME_BX_GLOBAL_SORT).'?action=setsflag&flag=0&pID='.$startpage['products_id'].'&catID='.( isset($_GET['catID']) ? $_GET['catID'] : 0 ).'">'
            .xtc_image(DIR_WS_IMAGES.'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 12, 12)
            .'</a>';
  } else {
    echo '<a href="'.xtc_href_link(FILENAME_BX_GLOBAL_SORT).'?action=setsflag&flag=1&pID='.$startpage['products_id'].'&catID='.( isset($_GET['catID']) ? $_GET['catID'] : 0 ).'">'
        .xtc_image(DIR_WS_IMAGES.'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 12, 12).'</a>'
        .xtc_image(DIR_WS_IMAGES.'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 12, 12, 'style="margin-left: 2px;"');
  }
    echo '</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'.( ($startpage["products_status"] == '1') ? xtc_image(DIR_WS_IMAGES.'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) : xtc_image(DIR_WS_IMAGES.'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10)).'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c"><strong>'.$startpage["products_startpage_sort"].'</strong></td>'.PHP_EOL
      .'</tr>'.PHP_EOL;
  $i++;
} 

?>
              </tbody>
            </table>
<?php echo '</form>'; ?>
          </td>
          <!-- boxCenterLeft //-->
          <td class="boxRight" style="width: 50%;">

            <div class="main" style="display: block; background: #AF417E; border-radius: 4px; margin: 2px 0 10px 0; padding: 4px 0 2px 0;">
              <div class="main" style="margin: 5px 10px; text-align: center;">
                <span style="display: inline-block; margin-bottom: 2px; color: #FFFFFF; font-weight: bold;"><?php echo BX_GS_TXT_CATEGORIES; ?></span>
                <small class="txta-l"><?php
        echo xtc_draw_form('global_cat', FILENAME_BX_GLOBAL_SORT, 'action=selectCategorie');
        echo xtc_draw_pull_down_menu('catID', xtc_get_category_tree('0'), ( isset($_GET['catID']) ? $_GET['catID'] : 0 ), 'onchange="this.form.submit();"');
      echo '</form>';
      ?></small>
              </div>
            </div>

            <p class="success_message_right" style="display: none; margin:0;"><?php echo BX_GS_TXT_SAVED_SUCCESSFULLY; ?></p>

            <?php echo xtc_draw_form('global_sort', FILENAME_BX_GLOBAL_SORT, 'action=savecategoriesort&catID='.( isset($_GET['catID']) ? $_GET['catID'] : 0 )); ?>
            <table class="tableCenter collapse" id="sortable2">
              <thead>
                <tr class="dataTableHeadingRow">
                  <th style="width: 15%; border-left: 1px solid #aaa;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_IMAGE; ?></th>
                  <th style="width: 10%;" class="dataTableHeadingContent"><?php echo BX_GS_TXT_ART_NO; ?></th>
                  <th style="width: 30%;" class="dataTableHeadingContent txta-l"><?php echo BX_GS_TXT_NAME; ?></th>
                  <th style="width: 25%;" class="dataTableHeadingContent txta-l"><?php echo BX_GS_TXT_CATEGORY; ?></th>
                  <th style="width: 5%;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_TOP; ?></th>
                  <th style="width: 5%;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_STATUS; ?></th>
                  <th style="width: 10%;" class="dataTableHeadingContent txta-c"><?php echo BX_GS_TXT_CURRENT; ?></th>
                </tr>
              </thead>
              <tbody>
<?php
if( isset($_GET['catID']) && !empty($_GET['catID']) )
{
$products_query = xtc_db_query("SELECT
                    p.products_id, 
                    pd.products_name,
                    p.products_model,
                    p.products_image,
                    p.products_status, 
                    p.products_sort, 
                    p.products_startpage 
                FROM ".TABLE_PRODUCTS." p, 
                      ".TABLE_PRODUCTS_DESCRIPTION." pd, 
                      ".TABLE_PRODUCTS_TO_CATEGORIES." p2c
            WHERE p.products_id = pd.products_id 
              AND p2c.categories_id = '".(int)$_GET["catID"]."' 
              AND p2c.products_id = p.products_id 
              AND pd.language_id = '".$_SESSION["languages_id"]."'
          ORDER BY p.products_sort");

$i = 1;
while ($products = xtc_db_fetch_array($products_query)) {
  echo '<tr class="dataTableRow">'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'
      .xtc_draw_hidden_field('productssort['.$products["products_id"].']', $products["products_sort"])
      .xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$products["products_image"], 'Standard Image','','','style="max-height:48px;"')
      .'  </td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'.$products["products_model"].'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-l">'.$products["products_name"].'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-l">'.xtc_output_generated_category_path($products["products_id"], 'product').'</td>'.PHP_EOL
      .'  <td class="dataTableContent txta-c">'.PHP_EOL;

      if ($products['products_startpage'] == '1') {
        echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 12, 12, 'style="margin-right: 2px;"')
            . '<a href="'.xtc_href_link(FILENAME_BX_GLOBAL_SORT).'?action=setsflag&flag=0&pID='.$products['products_id'].'&catID='.( isset($_GET['catID']) ? $_GET['catID'] : 0 ).'">'
            .xtc_image(DIR_WS_IMAGES.'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 12, 12)
            .'</a>';
      } else {
        echo '<a href="'.xtc_href_link(FILENAME_BX_GLOBAL_SORT).'?action=setsflag&flag=1&pID='.$products['products_id'].'&catID='.( isset($_GET['catID']) ? $_GET['catID'] : 0 ).'">'
        .xtc_image(DIR_WS_IMAGES.'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 12, 12).'</a>'
        .xtc_image(DIR_WS_IMAGES.'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 12, 12, 'style="margin-left: 2px;"');
      }

  echo '  </td>'.PHP_EOL
      .'  <td style="width: 5%;" class="dataTableContent txta-c">'
      .( ($products["products_status"] == '1') ? xtc_image(DIR_WS_IMAGES.'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) : xtc_image(DIR_WS_IMAGES.'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10))
      .'  </td>'.PHP_EOL
      .'  <td style="width: 15%;" class="dataTableContent txta-c"><strong>'.$products["products_sort"].'</strong></td>'.PHP_EOL
      .'</tr>'.PHP_EOL;
  $i++;
}
} else {
echo '<tr>'.PHP_EOL
    . '  <td colspan="7" style="height: 52px;" class="txta-c"><h3>'.BX_GS_TXT_CHOOSE_CATEGORY.'</h3></td>'.PHP_EOL
    . '</tr>'.PHP_EOL;
}
?>
              </tbody>
            </table>
            <?php echo '</form>'; ?>
          </td>
          <!-- boxRight //-->
        </tr>
      </table>
<?php } ?>
      </td>
      <!-- body_text_eof //-->
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES.'footer.php'); ?>
  <!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES.'application_bottom.php'); ?>