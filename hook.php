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
    $table_name_taskfield = "glpi_plugin_iistools_tasks";

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

    //add table for extra fields (task)
    
    if (!$DB->tableExists($table_name_taskfield)) {
        $query = "CREATE TABLE `$table_name_taskfield` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `ticket_task_id` INT(11) NOT NULL,
                    `duedate` DATE DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY (`ticket_task_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->query($query) or die("Error creating $table_name_taskfield table: " . $DB->error());
    }


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

    function plugin_iistools_taskForm($item, $options = []) {
        global $DB;

        if (!$item['item'] instanceof TicketTask ) {
            return;
        }

        $task = $item['item'];
        //$ticket_id = $task->fields['tickets_id'];
        $ticket_task_id = $task->fields['id'];
        $query = "SELECT duedate FROM glpi_plugin_iistools_tasks WHERE ticket_task_id = $ticket_task_id";
        $result = $DB->query($query);
        $duedate = ($result && $DB->numrows($result) > 0) ? $DB->result($result, 0, 'duedate') : '';

        echo '

                        <div class="form-field row col-12  mb-2">
                            <label for="plugin_iistools_duedate">'.__("We are done date:", 'iistools').'</label>
                            <input type="date" name="plugin_iistools_duedate" id="plugin_iistools_duedate" class="form-control" value="'.$duedate.'">
                        </div>
        ';
    }

    function plugin_iistools_itemAdd($item) {
        global $DB;
        $ticket_task_id = $item->fields['id'];
        if (isset($_POST['plugin_iistools_duedate'])) {
            if($_POST['plugin_iistools_duedate']!=""){
                $duedate = $DB->escape($_POST['plugin_iistools_duedate']);

                $check = $DB->request([
                    'FROM'  => 'glpi_plugin_iistools_tasks',
                    'WHERE' => ['ticket_task_id' => $ticket_task_id]
                ]);

                if (count($check)) {
                    $DB->update(
                        'glpi_plugin_iistools_tasks',
                        ['duedate' => $duedate],
                        ['ticket_task_id' => $ticket_task_id]
                    );
                } else {
                    $DB->insert(
                        'glpi_plugin_iistools_tasks',
                        ['ticket_task_id' => $ticket_task_id, 'duedate' => $duedate]
                    );
                }
            }else{
                $check = $DB->request([
                    'FROM'  => 'glpi_plugin_iistools_tasks',
                    'WHERE' => ['ticket_task_id' => $ticket_task_id]
                ]);
                if (count($check)) {
                    $DB->delete(
                        'glpi_plugin_iistools_tasks',
                        ['ticket_task_id' => $ticket_task_id]
                    );
                }
            }
        }
            
    }

    function plugin_iistools_save_task_duedate(TicketTask $task) {
        global $DB;
        var_dump($task);
        echo "asdfsadf";
        exit();
        $task_id = $task->getID();
        /*
        
        */
    }

    /*
    

            
            Array
( 
    [item] => TicketTask Object
        (
            [type:protected] => -1
            [displaylist:protected] => 1
            [showdebug] => 
            [taborientation] => vertical
            [get_item_to_display_tab] => 1
            [fields] => Array
                (
                    [id] => 
                    [uuid] => 
                    [tickets_id] => 1
                    [taskcategories_id] => 
                    [date] => 
                    [users_id] => 
                    [users_id_editor] => 
                    [content] => 
                    [is_private] => 
                    [actiontime] => 
                    [begin] => 
                    [end] => 
                    [state] => 1
                    [users_id_tech] => 
                    [groups_id_tech] => 
                    [date_mod] => 
                    [date_creation] => 
                    [tasktemplates_id] => 
                    [timeline_position] => 
                    [sourceitems_id] => 
                    [sourceof_items_id] => 
                )

            [input] => Array
                (
                    [tickets_id] => 1
                    [entities_id] => 0
                    [is_recursive] => 0
                )

            [updates] => Array
                (
                )

            [oldvalues] => Array
                (
                )

            [dohistory] => 
            [history_blacklist] => Array
                (
                )

            [auto_message_on_action] => 
            [no_form_page] => 
            [additional_fields_for_dictionnary] => Array
                (
                )

            [fkfield:protected] => 
            [searchopt:protected] => 
            [usenotepad:protected] => 
            [deduplicate_queued_notifications] => 1
            [right] => 
        )

    [options] => Array
        (
            [parent] => Ticket Object
                (
                    [type:protected] => -1
                    [displaylist:protected] => 1
                    [showdebug] => 
                    [taborientation] => vertical
                    [get_item_to_display_tab] => 1
                    [fields] => Array
                        (
                            [id] => 1
                            [entities_id] => 1
                            [name] => Teszt hibajegy igénylés (ticket1)
                            [date] => 2024-09-09 12:04:01
                            [closedate] => 
                            [solvedate] => 
                            [takeintoaccountdate] => 2024-09-09 12:07:20
                            [date_mod] => 2025-01-23 15:16:08
                            [users_id_lastupdater] => 2
                            [status] => 1
                            [users_id_recipient] => 7
                            [requesttypes_id] => 1
                            [content] => <p>Valami teszt hibajegy igénylés</p>
                            [urgency] => 3
                            [impact] => 3
                            [priority] => 3
                            [itilcategories_id] => 0
                            [type] => 2
                            [global_validation] => 1
                            [slas_id_ttr] => 0
                            [slas_id_tto] => 0
                            [slalevels_id_ttr] => 0
                            [time_to_resolve] => 
                            [time_to_own] => 
                            [begin_waiting_date] => 
                            [sla_waiting_duration] => 0
                            [ola_waiting_duration] => 0
                            [olas_id_tto] => 0
                            [olas_id_ttr] => 0
                            [olalevels_id_ttr] => 0
                            [ola_tto_begin_date] => 
                            [ola_ttr_begin_date] => 
                            [internal_time_to_resolve] => 
                            [internal_time_to_own] => 
                            [waiting_duration] => 4870161
                            [close_delay_stat] => 0
                            [solve_delay_stat] => 0
                            [takeintoaccount_delay_stat] => 199
                            [actiontime] => 45000
                            [is_deleted] => 0
                            [locations_id] => 0
                            [validation_percent] => 0
                            [date_creation] => 2024-09-09 12:04:01
                        )

                    [input] => Array
                        (
                        )

                    [updates] => Array
                        (
                        )

                    [oldvalues] => Array
                        (
                        )

                    [dohistory] => 1
                    [history_blacklist] => Array
                        (
                        )

                    [auto_message_on_action] => 1
                    [no_form_page] => 
                    [additional_fields_for_dictionnary] => Array
                        (
                        )

                    [fkfield:protected] => 
                    [searchopt:protected] => 
                    [usenotepad:protected] => 
                    [deduplicate_queued_notifications] => 
                    [right] => 
                    [lazy_loaded_users:protected] => Array
                        (
                            [1] => Array
                                (
                                    [0] => Array
                                        (
                                            [id] => 1
                                            [tickets_id] => 1
                                            [users_id] => 7
                                            [type] => 1
                                            [use_notification] => 1
                                            [alternative_email] => 
                                        )

                                )

                        )

                    [userlinkclass] => Ticket_User
                    [lazy_loaded_groups:protected] => 
                    [grouplinkclass] => Group_Ticket
                    [lazy_loaded_suppliers:protected] => 
                    [supplierlinkclass] => Supplier_Ticket
                    [userentity_oncreate:protected] => 1
                    [last_clone_index:protected] => 
                    [hardwaredatas] => Array
                        (
                        )

                    [computerfound] => 0
                )

            [_target] => /glpi/front/ticket.form.php
            [id] => 1
            [withtemplate] => 
            [_saved] => Array
                (
                )

            [_users_id_requester] => 2
            [_users_id_requester_notif] => Array
                (
                    [use_notification] => Array
                        (
                            [0] => 1
                        )

                    [alternative_email] => Array
                        (
                            [0] => 
                        )

                )

            [_groups_id_requester] => 0
            [_users_id_assign] => 2
            [_users_id_assign_notif] => Array
                (
                    [use_notification] => Array
                        (
                            [0] => 1
                        )

                    [alternative_email] => Array
                        (
                            [0] => 
                        )

                )

            [_groups_id_assign] => 0
            [_users_id_observer] => 0
            [_users_id_observer_notif] => Array
                (
                    [use_notification] => Array
                        (
                            [0] => 1
                        )

                    [alternative_email] => Array
                        (
                            [0] => 
                        )

                )

            [_groups_id_observer] => 0
            [_link] => Array
                (
                    [tickets_id_2] => 
                    [link] => 
                )

            [_suppliers_id_assign] => 0
            [_suppliers_id_assign_notif] => Array
                (
                    [use_notification] => Array
                        (
                            [0] => 1
                        )

                    [alternative_email] => Array
                        (
                            [0] => 
                        )

                )

            [name] => 
            [content] => 
            [itilcategories_id] => 0
            [urgency] => 3
            [impact] => 3
            [priority] => 3
            [requesttypes_id] => 1
            [actiontime] => 0
            [date] => NULL
            [entities_id] => 0
            [status] => 1
            [followup] => Array
                (
                )

            [itemtype] => 
            [items_id] => 0
            [locations_id] => 0
            [plan] => Array
                (
                )

            [global_validation] => 1
            [time_to_resolve] => NULL
            [time_to_own] => NULL
            [slas_id_tto] => 0
            [slas_id_ttr] => 0
            [internal_time_to_resolve] => NULL
            [internal_time_to_own] => NULL
            [olas_id_tto] => 0
            [olas_id_ttr] => 0
            [_add_validation] => 0
            [users_id_validate] => Array
                (
                )

            [type] => 1
            [_documents_id] => Array
                (
                )

            [_tasktemplates_id] => Array
                (
                )

            [_content] => Array
                (
                )

            [_tag_content] => Array
                (
              …
    
    */