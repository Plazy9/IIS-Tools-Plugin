<?php
use Migration;
use GlpiPlugin\Iistools\iisCars;
use GlpiPlugin\Iistools\iisCameras;
use GlpiPlugin\Iistools\iisMachineries;

function plugin_iistools_install() {
    global $DB;

    $migration = new Migration(Plugin::getInfo('iistools', 'version'));
    
    $table_name_car= "glpi_plugin_iistools_iiscars";
    $table_name_machine= "glpi_plugin_iistools_iismachineries";
    $table_name_camera= "glpi_plugin_iistools_iiscameras";

    $default_charset = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

    if (!$DB->tableExists($table_name_car)) {
        $query = "CREATE TABLE `$table_name_car` (
                            `id` int {$default_key_sign} NOT NULL auto_increment,
                            `license_plate` VARCHAR(16) NOT NULL,
                            `brand` VARCHAR(32) NULL DEFAULT NULL,
                            `type` VARCHAR(255) NULL DEFAULT NULL,
                            `key_count` INT(11) NULL DEFAULT NULL,
                            `key_registry_number` VARCHAR(255) NULL DEFAULT NULL,
                            `cost_center` VARCHAR(255) NULL DEFAULT NULL,
                            `handler` VARCHAR(255) NULL DEFAULT NULL,
                            `registration_license_number` VARCHAR(255) NULL DEFAULT NULL,
                            `technical_validity` DATE NULL DEFAULT NULL,
                            `email` VARCHAR(255) NULL DEFAULT NULL,
                            `service_name` VARCHAR(255) NULL DEFAULT NULL,
                            `service_address` TEXT NULL DEFAULT NULL,
                            `service_phone` VARCHAR(50) NULL DEFAULT NULL,
                            `service_email` VARCHAR(255) NULL DEFAULT NULL,
                            `contact_person` VARCHAR(255) NULL DEFAULT NULL,
                            `warranty_period` VARCHAR(255) NULL DEFAULT NULL,
                            `financing_type` ENUM('Leasing', 'Own', 'Rented', 'Other') ,
                            `fuel_type` ENUM('Petrol', 'Diesel', 'Hybrid', 'Electric', 'LPG') ,
                            `year` YEAR NULL DEFAULT NULL ,
                            `commissioning_date` DATE NULL DEFAULT NULL,
                            `commissioning_place` VARCHAR(255) NULL DEFAULT NULL,
                            `acquisition_date` DATE NULL DEFAULT NULL,
                            `acquisition_place` VARCHAR(255) NULL DEFAULT NULL,
                            `primary_driver` INT UNSIGNED NULL DEFAULT NULL,
                            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->query($query) or die("Error creating $table_name_car table: " . $DB->error());
    }


    if (!$DB->tableExists($table_name_machine)) {
        $query = "CREATE TABLE `$table_name_machine` (
                            `id` int {$default_key_sign} NOT NULL auto_increment,
                            `name` VARCHAR(64) NOT NULL,
                            `type` VARCHAR(255) ,
                            `manufacturer` VARCHAR(64) ,
                            `commissioning_date` DATE NULL DEFAULT NULL,
                            `commissioning_location` VARCHAR (255),
                            `serial_number` VARCHAR(255) ,
                            `custom_id` VARCHAR(255) ,
                            `location` VARCHAR(255) ,
                            `maintenance_user` INT UNSIGNED,
                            `cost_center` VARCHAR(255),
                            `responsible_user` INT UNSIGNED,
                            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->query($query) or die("Error creating $table_name_machine table: " . $DB->error());
    }

    if (!$DB->tableExists($table_name_camera)) {
        $query = "CREATE TABLE `$table_name_camera` (
                            `id` int {$default_key_sign} NOT NULL auto_increment,
                            `manufacturer` VARCHAR(64) ,
                            `type` VARCHAR(255) ,
                            `serial_number` VARCHAR(255) ,
                            `commissioning_date` DATE NULL DEFAULT NULL,
                            `commissioning_location` VARCHAR (255),
                            `ip` VARCHAR(16) ,
                            `gateway` VARCHAR(16) ,
                            `subnetmask` VARCHAR(16) ,
                            `dns1` VARCHAR(16),
                            `dns2` VARCHAR(16),
                            `port` int(5),
                            `http` VARCHAR(255),
                            `installation_person` VARCHAR(255),
                            `installation_company` VARCHAR(255),
                            `name` VARCHAR(255),
                            `status` boolean,
                            `cloud_status` boolean,
                            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->query($query) or die("Error creating $table_name_camera table: " . $DB->error());
    }

    // $migration->addRight() does not allow to copy an existing right, we must write some custom code
    $right_exist = countElementsInTable(
        "glpi_profilerights",
        ["name" => iisCars::$rightname]
    ) > 0;

   // Add the same standard rights on alerts as the rights already granted on
   // public reminders
//// Grants full access to profiles that can update the Config (super-admins)
//   $migration->addRight(Example::$rightname, ALLSTANDARDRIGHT, [Config::$rightname => UPDATE]);

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

    // install default display preferences
    $dpreferences = new DisplayPreference();
    $found_dpref = $dpreferences->find(['itemtype' => ['LIKE', 'Iistools']]);
    if (count($found_dpref) == 0) {
        $query ="INSERT INTO `glpi_displaypreferences`
                     (`itemtype`, `num`, `rank`, `users_id`)
                  VALUES
                     ('".addslashes(iisCars::getType())."', 1, 1, 0),
                     ('".addslashes(iisCars::getType())."', 2, 2, 0),
                     ('".addslashes(iisCars::getType())."', 5, 3, 0),
                     ('".addslashes(iisCars::getType())."', 6, 4, 0),
                     ('".addslashes(iisMachineries::getType())."', 2, 1, 0),
                     ('".addslashes(iisCameras::getType())."', 1, 1, 0),
                     ('".addslashes(iisCameras::getType())."', 2, 2, 0)
                     ";

        $DB->query( new QueryExpression($query));
    }


    $migration->executeMigration();

    return true;
}

function plugin_iistools_uninstall() {  
    global $DB;
    $table_name_car='glpi_plugin_iistools_iiscars';
    $table_name_machine= "glpi_plugin_iistools_iismachineries";
    $table_name_camera= "glpi_plugin_iistools_iiscameras";
    
    if ($DB->tableExists("$table_name_car")) {
        $query = "DROP TABLE `$table_name_car`";
        $DB->query($query) or die("Error dropping $table_name_car table: " . $DB->error());
    }

    if ($DB->tableExists("$table_name_machine")) {
        $query = "DROP TABLE `$table_name_machine`";
        $DB->query($query) or die("Error dropping $table_name_machine table: " . $DB->error());
    }

    if ($DB->tableExists("$table_name_camera")) {
        $query = "DROP TABLE `$table_name_camera`";
        $DB->query($query) or die("Error dropping $table_name_camera table: " . $DB->error());
    }

    $DB->query("DELETE FROM `glpi_profilerights` WHERE `name` LIKE '%plugin_iistools%';");
    $DB->query("DELETE FROM `glpi_displaypreferences` WHERE `itemtype` LIKE '%Iistools%';");

    return true;
}

function plugin_iistools_giveItem($type, $ID, $data, $num) {
   $searchopt = &Search::getOptions($type);
   $table = $searchopt[$ID]["table"];
   $field = $searchopt[$ID]["field"];

   $table_name_car= "glpi_plugin_iistools_iiscars";
   $table_name_machine= "glpi_plugin_iistools_iismachineries";
   $table_name_camera= "glpi_plugin_iistools_iiscameras";

   switch ($table.'.'.$field) {
        case $table_name_car.".license_plate" :
            $out = "<a href='".Toolbox::getItemTypeFormURL(iisCars::class)."?id=".$data['id']."'>";
            $out .= $data[$num][0]['name'];
            if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
            $out .= " (".$data["id"].")";
            }
            $out .= "</a>";
            return $out;
        case $table_name_machine.".name" :
            $out = "<a href='".Toolbox::getItemTypeFormURL(iisMachineries::class)."?id=".$data['id']."'>";
            $out .= $data[$num][0]['name'];
            if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
               $out .= " (".$data["id"].")";
            }
            $out .= "</a>";
            return $out;
        case $table_name_camera.".name" :
        case $table_name_camera.".ip" :
            $out = "<a href='".Toolbox::getItemTypeFormURL(iisCameras::class)."?id=".$data['id']."'>";
            $out .= $data[$num][0]['name'];
            if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
                $out .= " (".$data["id"].")";
            }
            $out .= "</a>";
            return $out;
   }
   return "";
}


 function plugin_iistools_AssignToTicket($types) {
    $types[iisCars::class] = iisCars::getTypeName();
    $types[iisMachineries::class] = iisMachineries::getTypeName();
    $types[iisCameras::class] = iisCameras::getTypeName();
    return $types;
 }
