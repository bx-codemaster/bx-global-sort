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
class bx_global_sort extends StdModule 
{
    /**
     * Module version used by StdModule update checks.
     *
     * @var string
     */
    public const VERSION = '2.0.5';
    
    /**
     * Configuration key prefix, e.g. `MODULE_BX_GLOBAL_SORT`.
     *
     * @var string
     */
    public $prefix;

    /** @var string Human readable configuration group title in admin UI. */
    private const CONFIG_GROUP_TITLE = 'BX Global Sort';

    /** @var string Description shown for the configuration group. */
    private const CONFIG_GROUP_DESCRIPTION = 'Settings for the BX Global Sort module';

    /**
     * Initialize module metadata and known configuration keys.
     *
     * @return void
     */
    public function __construct() 
    {
        parent::__construct('MODULE_BX_GLOBAL_SORT');
        
        $this->prefix = 'MODULE_' . strtoupper(self::class);
        $this->addKey('STATUS');

        $this->checkForUpdate(true);
    }

    /**
     * Render module action buttons in system module UI.
     *
     * @return array<string, string>
     */
    public function display(): array 
    {
        return $this->displaySaveButton();
    }

    /**
     * Install module related permissions and configuration entries.
     *
     * Installation flow:
     * 1. Run base StdModule installation.
     * 2. Grant admin access column for this module.
     * 3. Resolve or create configuration group ID.
     * 4. Insert all required configuration keys.
     * 5. Perform additional database modifications.
     *
     * @return void
     */
    public function install(): void {
        parent::install();

        $this->setAdminAccess(self::class);

        $groupId = $this->getOrCreateConfigGroupId();

        $this->addConfiguration('STATUS', 'true', $groupId, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');

        // Additional database modifications
        xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " ADD bx_global_sort INTEGER(1)");
        xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET bx_global_sort = 1");
        xtc_db_query("UPDATE `" . TABLE_CATEGORIES . "` SET `products_sorting` = 'p.products_sort'");
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD INDEX(`products_sort`)");
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD INDEX(`products_startpage_sort`)");
    }

    /**
     * Remove module configuration and admin access flag.
     *
     * @return void
     */
    public function remove(): void {
        // Additional database cleanup
        xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " DROP bx_global_sort;");
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " DROP INDEX `products_sort`");
        xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " DROP INDEX `products_startpage_sort`");
        xtc_db_query("UPDATE `" . TABLE_CATEGORIES . "` SET `products_sorting` = 'pd.products_name'");

        $this->removeConfigurationAll();

        $this->removeConfigurationGroup();

        $this->deleteAdminAccess(self::class);

        parent::remove();
    }

    protected function updateSteps()
    {
        $currentVersion = $this->getVersion();
        if (!$currentVersion) {
            $this->setVersion('2.0.5');
            return StdModule::UPDATE_SUCCESS;
        }

        return StdModule::UPDATE_NOTHING;
    }

    /**
     * Resolve existing configuration group ID or create a new one.
     *
     * If the group does not exist, the method determines the lowest free
     * `configuration_group_id`, inserts a new group row, and returns that ID.
     *
     * @return int Configuration group ID for this module.
     */
    private function getOrCreateConfigGroupId(): int {
        $query = xtc_db_query(
            "SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '" . self::CONFIG_GROUP_TITLE . "' LIMIT 1"
        );
        $row = xtc_db_fetch_array($query);

        if (!empty($row['configuration_group_id'])) {
            return (int)$row['configuration_group_id'];
        }

        $idQuery = xtc_db_query(
            "SELECT COALESCE((SELECT 1 WHERE NOT EXISTS (SELECT 1 FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = 1)), (SELECT MIN(t1.configuration_group_id + 1) FROM " . TABLE_CONFIGURATION_GROUP . " t1 WHERE (t1.configuration_group_id + 1) NOT IN (SELECT t2.configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " t2 WHERE t2.configuration_group_id IS NOT NULL)), 1) AS id");

        $idRow = xtc_db_fetch_array($idQuery);
        $nextGroupId = isset($idRow['id']) ? (int)$idRow['id'] : 1;

        $sortQuery = xtc_db_query(
            "SELECT (sort_order+1) AS next_sort_order FROM ".TABLE_CONFIGURATION_GROUP." WHERE (sort_order+1) NOT IN (SELECT sort_order FROM ".TABLE_CONFIGURATION_GROUP." WHERE sort_order IS NOT NULL) LIMIT 1");
        
        $sortRow   = xtc_db_fetch_array($sortQuery);
        $sortOrder = isset($sortRow['next_sort_order']) ? (int)$sortRow['next_sort_order'] : 1;

        xtc_db_query(
            "INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (
                configuration_group_id,
                configuration_group_title,
                configuration_group_description,
                sort_order,
                visible
            ) VALUES (
                '" . $nextGroupId . "',
                '" . self::CONFIG_GROUP_TITLE . "',
                '" . self::CONFIG_GROUP_DESCRIPTION . "',
                '" . $sortOrder . "',
                1
            )"
        );

        return $nextGroupId;
    }

    protected function removeConfigurationGroup(): bool
    {
        $remove_group_query = xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '" . self::CONFIG_GROUP_TITLE ."'");

        $success = false !== $remove_group_query;

        return $success;
    }
}
