<?php

function plugin_iistools_install() {
    global $DB;

    $migration = new Migration(PLUGIN_IISTOOLS_VERSION);
    
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


    return true;
}

function plugin_iistools_uninstall() {  
    global $DB;

    if ($DB->tableExists("$table_name_car")) {
        $query = "DROP TABLE `$table_name_car`";
        $DB->query($query) or die("Error dropping $table_name_car table: " . $DB->error());
    }

    return true;
}