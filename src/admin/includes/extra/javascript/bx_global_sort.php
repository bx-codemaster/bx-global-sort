<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Global Sort - JavaScript
 * 
 * jQuery UI Sortable Integration mit automatischer AJAX-Speicherung.
 * Enthält:
 * - Sortable-Konfiguration für Tabellen (tbody Drag & Drop)
 * - AJAX Auto-Save Funktion (saveSortOrder) mit Toast-Notifications
 * - Placeholder-Styling während des Dragging (7 Spalten)
 * - Automatische Sort-Order-Aktualisierung in Echtzeit
 * - Visuelles Feedback (saving/saved/error States)
 * - jQuery 1.8.3 kompatible POST-Request-Serialisierung
 * 
 * @package    BX Global Sort
 * @subpackage JavaScript
 * @category   Admin
 * @author     Axel Benkert
 * @version    1.2
 * @since      1.0.0
 * @date       2025-11-09
 * @copyright  2020-2025 Axel Benkert
 * @license    GNU General Public License
 * @requires   jQuery 1.8.3+, jQuery UI 1.12.1 
 */

 defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

 if (defined('MODULE_BX_GLOBAL_SORT_STATUS') && MODULE_BX_GLOBAL_SORT_STATUS == 'true' && basename($_SERVER['PHP_SELF']) == 'bx_global_sort.php') {
?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script>
  "use strict";
  // Toast Notification Function
  function showToast(message, type) {
    type = type || 'success';
    var iconClass = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
    
    var toast = $('<div class="bx-toast ' + type + '">' +
      '<i class="fas ' + iconClass + ' bx-toast-icon"></i>' +
      '<div class="bx-toast-message">' + message + '</div>' +
      '<span class="bx-toast-close">&times;</span>' +
    '</div>');
    
    $('body').append(toast);
    
    toast.find('.bx-toast-close').on('click', function() {
      toast.addClass('hiding');
      setTimeout(function() { toast.remove(); }, 300);
    });
    
    setTimeout(function() {
      toast.addClass('hiding');
      setTimeout(function() { toast.remove(); }, 300);
    }, 4000);
  }
  
  // AJAX Save Function - saves ALL rows in the table
  function saveSortOrder(row, type) {
    row.removeClass('saved error').addClass('saving');
    
    // Get current catID from URL if present
    var urlParams = new URLSearchParams(window.location.search);
    var catID     = urlParams.get('catID') || '';
    var ajaxUrl   = '<?php echo FILENAME_BX_GLOBAL_SORT; ?>' + (catID ? '?catID=' + catID : '');
    
    // Collect ALL products with their new sort order
    var sortData = [];
    var tbody = row.closest('tbody');
    tbody.find('tr').each(function(index) {
      var inputField = $(this).find('input[name^="startsort"], input[name^="productssort"]');
      if (inputField.length > 0) {
        var fieldName = inputField.attr('name');
        var productsId = fieldName.match(/\[(\d+)\]/);
        if (productsId && productsId[1]) {
          sortData.push({
            products_id: parseInt(productsId[1]),
            sort_order: index + 1
          });
        }
      }
    });
    
    // Build POST data with all products
    var postData = 'action=ajaxSaveSortBatch'
                 + '&type=' + type
                 + '&sort_data=' + encodeURIComponent(JSON.stringify(sortData))
                 + '&<?php echo $_SESSION['CSRFName']; ?>=' + '<?php echo $_SESSION['CSRFToken']; ?>';
    
    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      dataType: 'json',
      contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      data: postData,
      success: function(response) {
        // Mark all rows as saved
        row.closest('tbody').find('tr').removeClass('saving').addClass('saved');
        setTimeout(function() {
          row.closest('tbody').find('tr').removeClass('saved');
        }, 2000);
        
        var successMsg = response.message || '<?php echo BX_GS_AJAX_SUCCESS_SAVED; ?>';
        if (response.updated_count) {
          successMsg += ' (' + response.updated_count + ' Produkte)';
        }
        showToast(successMsg, 'success');
      },
      error: function(xhr) {
        row.removeClass('saving').addClass('error');
        setTimeout(function() { row.removeClass('error'); }, 3000);
        
        var errorMsg = '<?php echo BX_GS_AJAX_ERROR_SAVING; ?>';
        try {
          var response = JSON.parse(xhr.responseText);
          errorMsg = response.message || errorMsg;
        } catch(e) {
          errorMsg = '<?php echo BX_GS_AJAX_ERROR_SERVER; ?>: ' + xhr.status;
        }
        
        showToast(errorMsg, 'error');
      }
    });
  }
  
  $( function() {
		$( "#sortable tbody, #sortable2 tbody" ).sortable({
			forcePlaceholderSize: true,
			placeholder: "state-highlight",
			items: '> tr',
			revert: true,
			scrollSensitivity: 50,
			helper: "clone",
			connectWith: '#sortable tbody',
			start: function (event, ui) {
        ui.placeholder.html('<td colspan="7" style="width: 100%;">&nbsp;</td>');
			},
			update: function( event, ui ) {
				var tbody = $(this);
				tbody.children().each(function(index) {
					var newindex = index + 1;
					$(this).find('td:first-child >input').val(newindex);
					$(this).find('td').last().html('<strong>'+newindex+'</strong>');
				});
			},
			stop: function(event, ui){
				var row = ui.item;
				var isStartpage = row.closest('#sortable').length > 0;
				var type = isStartpage ? 'startpage' : 'category';
				
				saveSortOrder(row, type);
			}
		})
		.disableSelection();
	});
</script>
<?php
 }
?>