<?php
use GlpiPlugin\Iistools\iisCars;

function plugin_iistools_install() {
    global $DB;

    //$migration = new Migration(PLUGIN_IISTOOLS_VERSION);
    $migration = new Migration(Plugin::getInfo('iistools', 'version'));
    
    $table_name_car= "glpi_plugin_iistools_iiscars";

    $default_charset = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

    if (!$DB->tableExists($table_name_car)) {
        $query = "CREATE TABLE `$table_name_car` (
            `id` int {$default_key_sign} NOT NULL auto_increment,
            `type` VARCHAR(255) NOT NULL,
            `color` VARCHAR(255) NOT NULL,
            `license_plate` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->query($query) or die("Error creating $table_name_car table: " . $DB->error());
    }

    // $migration->addRight() does not allow to copy an existing right, we must write some custom code
    $right_exist = countElementsInTable(
        "glpi_profilerights",
        ["name" => iisCars::$rightname]
    ) > 0;

   // Add the same standard rights on alerts as the rights already granted on
   // public reminders

    if (!$right_exist) {
        $reminder_rights = $DB->request([
            'SELECT' => ['profiles_id', 'rights'],
            'FROM'   => 'glpi_profilerights',
            'WHERE'  => ['name' => 'reminder_public']
        ]);

        foreach ($reminder_rights as $row) {
            $profile_id  = $row['profiles_id'];
            $right_value = $row['rights'] & ALLSTANDARDRIGHT;

            $migration->addPostQuery($DB->buildInsert('glpi_profilerights', [
                'profiles_id' => $profile_id,
                'rights'      => $right_value,
                'name'        => iisCars::$rightname,
            ]));

            if (($_SESSION['glpiactiveprofile']['id'] ?? null) === $profile_id) {
                 // Ensure menu will be displayed as soon as right is added.
                 $_SESSION['glpiactiveprofile'][iisCars::$rightname] = $right_value;
                 unset($_SESSION['glpimenu']);
            }
        }
    }

    $migration->executeMigration();

    return true;
}

function plugin_iistools_uninstall() {  
    global $DB;

    if ($DB->tableExists("$table_name_car")) {
        $query = "DROP TABLE `$table_name_car`";
        $DB->query($query) or die("Error dropping $table_name_car table: " . $DB->error());
    }

    $DB->query("DELETE FROM `glpi_profilerights` WHERE `name` LIKE '%plugin_iistools%';");

    return true;
}