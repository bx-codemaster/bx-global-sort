<?php
/**
 * BX Global Sort - System Module configuration class.
 *
 * This file contains the `bx_global_sort` system module for modified eCommerce.
 * The module integrates with RobinTheHood StdModule and is responsible for:
 * - module bootstrap and metadata registration
 * - installation and removal database routines
 * - admin access flag provisioning
 * - configuration group creation in `TABLE_CONFIGURATION_GROUP`
 * - configuration key creation in `TABLE_CONFIGURATION`
 *
 * Implementation notes:
 * - The module prefix is generated dynamically from class name and reused for
 *   all module-specific configuration keys.
 * - Configuration group IDs are allocated using the lowest free numeric ID.
 * - The class intentionally keeps SQL statements explicit so behavior is
 *   transparent and easy to audit in legacy modified environments.
 *
 * @package    BX Global Sort
 * @subpackage Admin/SystemModule
 * @author     Axel Benkert
 * @license    GNU General Public License
 * @since      2.0.5
 */

use RobinTheHood\ModifiedStdModule\Classes\StdModule;

/**
 * System module class for BX Global Sort.
 *
 * The class extends `StdModule` and adds installation/removal routines for
 * module specific configuration and access rights.
 */
class bx_global_sort
{
    public string $code;
    public string $version;
    public string $development_status;
    public string $title;
    public string $description;
    public int $sort_order;
    public bool $enabled;
    private bool $_check;

    private const CONFIG_GROUP_TITLE = 'BX Global Sort';
    private const CONFIG_GROUP_DESCRIPTION = 'Settings for the BX Global Sort module';

    public function __construct() {
        $this->code = 'bx_global_sort';
        $this->version = '2.0.5';
        $this->development_status = 'p';
        $this->title = MODULE_BX_GLOBAL_SORT_TITLE;
        $this->description = constant('MODULE_BX_GLOBAL_SORT_DESC');
        $this->sort_order = defined('MODULE_BX_GLOBAL_SORT_SORT_ORDER') ? (int)constant('MODULE_BX_GLOBAL_SORT_SORT_ORDER') : 0;
        $this->enabled = defined('MODULE_BX_GLOBAL_SORT_STATUS') && constant('MODULE_BX_GLOBAL_SORT_STATUS') == 'True';
    }

    public function check(): bool {
        if (!isset($this->_check)) {
            if (defined('MODULE_BX_GLOBAL_SORT_STATUS')) {
                $this->_check = true;
            } else {
                $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_BX_GLOBAL_SORT_STATUS'");
                $this->_check = xtc_db_num_rows($check_query);
            }
        }

        return $this->_check;
    }

    public function keys(): array {
        return array(
            'MODULE_BX_GLOBAL_SORT_STATUS',
        );
    }

    public function install(): void {
        xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " ADD " . $this->code . " INTEGER(1) DEFAULT 0");
        xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET " . $this->code . " = 1");

        $groupId = $this->getOrCreateConfigGroupId();

        $query = "INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_key,
                    configuration_value,
                    configuration_group_id,
                    sort_order,
                    date_added,
                    use_function,
                    set_function
                ) VALUES (
                    'MODULE_BX_GLOBAL_SORT_STATUS',
                    'True',
                    '" . (int)$groupId . "',
                    '1',
                    NOW(),
                    '',
                    'xtc_cfg_select_option(array(\\'True\\', \\'False\\'), '
                )";
        xtc_db_query($query);

        xtc_db_query("UPDATE `" . TABLE_CATEGORIES . "` SET `products_sorting` = 'p.products_sort'");
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD INDEX(`products_sort`)");
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD INDEX(`products_startpage_sort`)");
    }

    public function update(): void {
    }

    public function remove(): void {
        $groupId = $this->getConfigurationGroupId();

        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");

        if ($groupId > 0) {
            xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = " . (int)$groupId);
        }

        xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " DROP " . $this->code);
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " DROP INDEX `products_sort`");
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " DROP INDEX `products_startpage_sort`");
        xtc_db_query("UPDATE `" . TABLE_CATEGORIES . "` SET `products_sorting` = 'pd.products_name'");
    }

    public function process(): void {
    }

    public function display(): array {
        return array('text' => '<div style="text-align: center;">' . xtc_button(BUTTON_SAVE) . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $this->code)) . "</div>");
    }

    public function custom(): void {
    }

    public function getConfigurationGroupId(): int {
        $query = xtc_db_query("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '" . self::CONFIG_GROUP_TITLE . "' LIMIT 1");
        $row = xtc_db_fetch_array($query);

        if (!empty($row['configuration_group_id'])) {
            return (int)$row['configuration_group_id'];
        }

        return 0;
    }

    private function getOrCreateConfigGroupId(): int {
        $groupId = $this->getConfigurationGroupId();
        if ($groupId > 0) {
            return $groupId;
        }

        $freeId_query = xtc_db_query("SELECT (configuration_group_id+1) AS id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE (configuration_group_id+1) NOT IN (SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id IS NOT NULL) LIMIT 1");
        $freeId = xtc_db_fetch_array($freeId_query);
        $newId = isset($freeId['id']) ? (int)$freeId['id'] : 1;

        $freeSort_query = xtc_db_query("SELECT (sort_order+1) AS sort_order FROM " . TABLE_CONFIGURATION_GROUP . " WHERE (sort_order+1) NOT IN (SELECT sort_order FROM " . TABLE_CONFIGURATION_GROUP . " WHERE sort_order IS NOT NULL) LIMIT 1");
        $freeSort = xtc_db_fetch_array($freeSort_query);
        $newSort = isset($freeSort['sort_order']) ? (int)$freeSort['sort_order'] : 1;

        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (
                configuration_group_id,
                configuration_group_title,
                configuration_group_description,
                sort_order,
                visible
            ) VALUES (
                " . $newId . ",
                '" . self::CONFIG_GROUP_TITLE . "',
                '" . self::CONFIG_GROUP_DESCRIPTION . "',
                " . $newSort . ",
                1
            )");

        return $newId;
    }
}
