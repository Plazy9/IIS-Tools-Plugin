<?php
//use Migration;
use GlpiPlugin\Iistools\iisCars;
use GlpiPlugin\Iistools\iisCameras;
use GlpiPlugin\Iistools\iisMachineries;
use GlpiPlugin\Iistools\iisCostReport;
use GlpiPlugin\Iistools\iisBarcode;

function plugin_iistools_install() {
    global $DB;

    if (!file_exists(GLPI_PLUGIN_DOC_DIR."/iistools")) {
        mkdir(GLPI_PLUGIN_DOC_DIR."/iistools");
     }

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

    if (!$DB->fieldExists($table_name_car, 'entities_id')) {
        $migration->addField(
            $table_name_car,
            'entities_id',
            'int'
        );
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

    if (!$DB->fieldExists($table_name_machine, 'entities_id')) {
        $migration->addField(
            $table_name_machine,
            'entities_id',
            'int'
        );
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
                            /*`http` VARCHAR(255),*/
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

    if ($DB->fieldExists($table_name_camera, 'http')) {
        $migration->dropField(
            $table_name_camera,
            'http',
        );
    }
    
    if (!$DB->fieldExists($table_name_camera, 'entities_id')) {
        $migration->addField(
            $table_name_camera,
            'entities_id',
            'int'
        );
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

    // set cameras rights
    $right_exist = countElementsInTable(
        "glpi_profilerights",
        ["name" => iisCameras::$rightname]
    ) > 0;

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
                'name'        => iisCameras::$rightname,
            ]));

            if (($_SESSION['glpiactiveprofile']['id'] ?? null) === $profile_id) {
                 // Ensure menu will be displayed as soon as right is added.
                 $_SESSION['glpiactiveprofile'][iisCameras::$rightname] = $right_value;
                 unset($_SESSION['glpimenu']);
            }
        }
    }

    // set machineries rights
    $right_exist = countElementsInTable(
        "glpi_profilerights",
        ["name" => iisMachineries::$rightname]
    ) > 0;

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
                'name'        => iisMachineries::$rightname,
            ]));

            if (($_SESSION['glpiactiveprofile']['id'] ?? null) === $profile_id) {
                 // Ensure menu will be displayed as soon as right is added.
                 $_SESSION['glpiactiveprofile'][iisMachineries::$rightname] = $right_value;
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
                     ('".addslashes(iisCars::getType())."', 80, 1, 0),
                     ('".addslashes(iisCars::getType())."', 1, 2, 0),
                     ('".addslashes(iisCars::getType())."', 2, 3, 0),
                     ('".addslashes(iisCars::getType())."', 3, 4, 0),
                     ('".addslashes(iisCars::getType())."', 7, 5, 0),
                     ('".addslashes(iisMachineries::getType())."', 80, 1, 0),
                     ('".addslashes(iisMachineries::getType())."', 1, 2, 0),
                     ('".addslashes(iisMachineries::getType())."', 2, 3, 0),
                     ('".addslashes(iisMachineries::getType())."', 3, 4, 0),
                     ('".addslashes(iisCameras::getType())."', 80, 1, 0),
                     ('".addslashes(iisCameras::getType())."', 1, 2, 0),
                     ('".addslashes(iisCameras::getType())."', 2, 3, 0),
                     ('".addslashes(iisCameras::getType())."', 3, 42, 0),
                     ('".addslashes(iisCostReport::getType())."', 2, 1, 0),
                     ('".addslashes(iisCostReport::getType())."', 3, 2, 0),
                     ('".addslashes(iisCostReport::getType())."', 5, 6, 0),
                     ('".addslashes(iisCostReport::getType())."', 7, 8, 0),
                     ('".addslashes(iisCostReport::getType())."', 32, 4, 0),
                     ('".addslashes(iisCostReport::getType())."', 9, 10, 0),
                     ('".addslashes(iisCostReport::getType())."', 10, 11, 0),
                     ('".addslashes(iisCostReport::getType())."', 11, 12, 0),
                     ('".addslashes(iisCostReport::getType())."', 12, 13, 0),
                     ('".addslashes(iisCostReport::getType())."', 13, 14, 0)
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
/*
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
*/
    $DB->query("DELETE FROM `glpi_profilerights` WHERE `name` LIKE '%plugin_iistools%';");
    $DB->query("DELETE FROM `glpi_displaypreferences` WHERE `itemtype` LIKE '%Iistools%';");

    return true;
}

function plugin_iistools_MassiveActions($itemtype){

    $actions = [];
    switch ($itemtype) {
        case 'Computer' :
            $myclass      = iisBarcode::class;
            $action_key   = 'Generate';
            $action_label = __("IIS QRcode Print PDF", 'iistools');
            $actions[$myclass.MassiveAction::CLASS_ACTION_SEPARATOR.$action_key] = $action_label;

            $action_key   = 'GenerateCSV';
            $action_label = __("IIS QRcode Print CSV", 'iistools');
            $actions[$myclass.MassiveAction::CLASS_ACTION_SEPARATOR.$action_key] = $action_label;

            $action_key   = 'GenerateXLS';
            $action_label = __("IIS QRcode Print XLS", 'iistools');
            $actions[$myclass.MassiveAction::CLASS_ACTION_SEPARATOR.$action_key] = $action_label;

            break;
    }
    return $actions;
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
        //report
        case "glpi_tickettasks.actiontime":
            return HTML::timestampToString($data[$num][0]['name'], false);
        case "glpi_tickettasks.content":
            return HTML::entity_decode_deep($data[$num][0]['name'], false);
        case "iis_ticketcost_table.cost_time":
            return $data['GlpiPlugin\Iistools\iisCostReport_10'][0]['name']/3600*$data[$num][0]['name'];
        case "iis_tickets_table.name":
            $out = "<a href='".Toolbox::getItemTypeFormURL(Ticket::class)."?id=".$data[$num][0]['id']."'>";
            $out .= $data[$num][0]['name'];
            if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
                $out .= " (".$data["id"].")";
            }
            $out .= "</a>";
            return $out;
        case "iis_entities_table.name":
            $out = "<a href='".Toolbox::getItemTypeFormURL(Entity::class)."?id=".$data[$num][0]['id']."'>";
            $out .= $data[$num][0]['name'];
            if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
                $out .= " (".$data["id"].")";
            }
            $out .= "</a>";
            return $out;
        
   }
   return "";
}

function plugin_iistools_addDefaultWhere($itemtype) {
    switch ($itemtype) {
       case iisCostReport::class:
          return  getEntitiesRestrictRequest('  ', 'iis_tickets_table');
    }
    return '';
 }

function plugin_iistools_addDefaultJoin($type, $ref_table, &$already_link_tables) {
    
    switch ($type) {
        case iisCostReport::class :
            return "LEFT JOIN glpi_tickets as iis_tickets_table ON (glpi_tickettasks.tickets_id = iis_tickets_table.id) ".
                   "LEFT JOIN glpi_entities AS iis_entities_table ON (iis_tickets_table.entities_id = iis_entities_table.id ) ".
                   "LEFT JOIN glpi_problems_tickets as iis_problems_table ON (iis_problems_table.tickets_id = glpi_tickettasks.tickets_id) ".
                   "LEFT JOIN glpi_ticketcosts as iis_ticketcost_table ON (SUBSTRING_INDEX(iis_ticketcost_table.name , '_', 1)=glpi_tickettasks.id)";
    }
    return "";
    }
    
 function plugin_iistools_AssignToTicket($types) {
    $types[iisCars::class] = iisCars::getTypeName();
    $types[iisMachineries::class] = iisMachineries::getTypeName();
    $types[iisCameras::class] = iisCameras::getTypeName();
    return $types;
 }
