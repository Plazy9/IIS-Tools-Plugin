<?php
use GlpiPlugin\Iistools\iisCars;
use GlpiPlugin\Iistools\iisMachineries;

function plugin_iistools_install() {
    global $DB;

    $migration = new Migration(Plugin::getInfo('iistools', 'version'));
    
    $table_name_car= "glpi_plugin_iistools_iiscars";
    $table_name_machine= "glpi_plugin_iistools_iismachineries";

    $default_charset = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

    if (!$DB->tableExists($table_name_car)) {
        $query = "CREATE TABLE `$table_name_car` (
                            `id` int {$default_key_sign} NOT NULL auto_increment,
                            `license_plate` VARCHAR(16) NOT NULL,
                            `brand` VARCHAR(32) ,
                            `type` VARCHAR(255) ,
                            `key_count` INT(11) ,
                            `key_registry_number` VARCHAR(255) ,
                            `cost_center` VARCHAR(255) ,
                            `handler` VARCHAR(255) ,
                            `registration_license_number` VARCHAR(255) ,
                            `technical_validity` DATE ,
                            `email` VARCHAR(255) ,
                            `service_name` VARCHAR(255) ,
                            `service_address` TEXT,
                            `service_phone` VARCHAR(50),
                            `service_email` VARCHAR(255),
                            `contact_person` VARCHAR(255),
                            `warranty_period` VARCHAR(255),
                            `financing_type` ENUM('Leasing', 'Own', 'Rented', 'Other') ,
                            `fuel_type` ENUM('Petrol', 'Diesel', 'Hybrid', 'Electric', 'LPG') ,
                            `year` YEAR ,
                            `commissioning_date` DATE ,
                            `commissioning_place` VARCHAR(255) ,
                            `acquisition_date` DATE ,
                            `acquisition_place` VARCHAR(255) ,
                            `primary_driver` INT UNSIGNED,
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
                            `commissioning_date` DATE,
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
                     ('".addslashes('GlpiPlugin\Iistools\iisCars')."', 1, 1, 0),
                     ('".addslashes('GlpiPlugin\Iistools\iisCars')."', 2, 2, 0),
                     ('".addslashes('GlpiPlugin\Iistools\iisCars')."', 5, 3, 0),
                     ('".addslashes('GlpiPlugin\Iistools\iisCars')."', 6, 4, 0)";

        $DB->query( new QueryExpression($query));
    }


    $migration->executeMigration();

    return true;
}

function plugin_iistools_uninstall() {  
    global $DB;
    $table_name_car='glpi_plugin_iistools_iiscars';
    $table_name_machine= "glpi_plugin_iistools_iismachineries";
    if ($DB->tableExists("$table_name_car")) {
        //$query = "DROP TABLE `$table_name_car`";
        //$DB->query($query) or die("Error dropping $table_name_car table: " . $DB->error());
    }

    //$DB->query("DELETE FROM `glpi_profilerights` WHERE `name` LIKE '%plugin_iistools%';");
    //$DB->query("DELETE FROM `glpi_displaypreferences` WHERE `itemtype` LIKE '%Iistools%';");

    return true;
}

function plugin_iistools_giveItem($type, $ID, $data, $num) {
   $searchopt = &Search::getOptions($type);
   $table = $searchopt[$ID]["table"];
   $field = $searchopt[$ID]["field"];

   $table_name_car= "glpi_plugin_iistools_iiscars";
   $table_name_machine= "glpi_plugin_iistools_iismachineries";

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
   }
   return "";
}