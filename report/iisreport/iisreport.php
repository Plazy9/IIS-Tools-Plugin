<?php

$USEDBREPLICATE         = 0;
$DBCONNECTION_REQUIRED  = 0;

include("../../../../inc/includes.php");

//TRANS: The name of the report = Users with no right
$report = new PluginReportsAutoReport(__('iisreport_report_title', 'iistools'));

//new PluginReportsTextCriteria($report, 'requester_user.users_id', __('Igénylő'));
new PluginReportsUserCriteria($report, 'requester_user.users_id', __('Requester user', 'iistools'));
new PluginReportsUserCriteria($report, 'assign_user.users_id', __('Assign user', 'iistools'));
new PluginReportsUserCriteria($report, 'observer_user.users_id', __('Observer user', 'iistools'));

$tab = [
    0 => __('No'),
    '' => __('Yes')
];
$filterWithClosed = new PluginReportsArrayCriteria($report, 'glpi_tickets.status', __('With closed ticket', 'iistools'), $tab);

include("../../inc/IisDateIntervalCriteria.php");

$dateCriteria = new IisDateIntervalCriteria($report, 'glpi_tickets.date');

$dateCriteria->addCriteriaLabel($dateCriteria->getName() . "_1", __('Created between', 'iistools'));
$dateCriteria->addCriteriaLabel($dateCriteria->getName() . "_2", '');

//Display criterias form is needed
$report->displayCriteriasForm();

//If criterias have been validated
if ($report->criteriasValidated()) {
    $report->setSubNameAuto();
    $report->delCriteria('glpi_tickets.status');

    $cols = [
        //new PluginReportsColumnItemCheckbox('id', 'Ticket'),
        new PluginReportsColumnLink('id2', __('Ticket', 'iistools'), 'Ticket', [
            'with_comment' => true,
            'with_navigate' => true
        ]),
        //new PluginReportsColumn('ticket_status', __('Ticket status'), ['sorton' => 'ticket_status']),
        new PluginReportsColumn('ticket_status2', __('Ticket status', 'iistools')),

        /*
        new PluginReportsColumn('recipient_userid', __('User id'), 'User', [
            'with_comment' => true,
            'with_navigate' => true
        ]),
        new PluginReportsColumnLink('recipient_userid', __('User'), 'User'),
        */

        new PluginReportsColumnLink('assign_user_users_id', __('Assign user', 'iistools'), 'User'),
        new PluginReportsColumnLink('requester_user_users_id', __('Requester user', 'iistools'), 'User'),
        new PluginReportsColumnLink('observer_user_users_id', __('Observer user', 'iistools'), 'User'),

        //new PluginReportsColumn('recipient_username', __('Recipient_user')),

        // new PluginReportsColumn('name', __('Login'), ['sorton' => 'name']),
        //new PluginReportsColumn('ticket_name', __('Ticket name', 'iistools')),
        new PluginReportsColumn('belsmunkaszmfield', __('Belső munkaszám', 'iistools'), ['sorton' => 'belsmunkaszmfield']),
        new PluginReportsColumnDate('ticket_date', __('Ticket create date', 'iistools'), ['sorton' => 'ticket_date']),
        new PluginReportsColumnDate('ticket_solvedate', __('Ticket solve date', 'iistools'), ['sorton' => 'ticket_solvedate']),
        new PluginReportsColumnDate('ticket_closedate', __('Ticket close date', 'iistools'), ['sorton' => 'ticket_closedate']),
        new PluginReportsColumnFloat('ticket_sum_time_cost', __('Ticket sum cost of time costs', 'iistools'), ['sorton' => 'ticket_sum_time_cost']),
        new PluginReportsColumnFloat('ticket_sum_fix_cost', __('Ticket sum cost of fixed costs', 'iistools'), ['sorton' => 'ticket_sum_fix_cost']),
        new PluginReportsColumnFloat('ticket_sum_material_cost', __('Ticket sum cost of material cost', 'iistools'), ['sorton' => 'ticket_sum_material_cost']),
        new PluginReportsColumnFloat('ticket_sum_cost', __('Ticket sum cost', 'iistools'), ['sorton' => 'ticket_sum_cost']),

    ];

    $report->setColumns($cols);

    $query = "Select glpi_tickets.id as 'id',
                glpi_tickets.id as 'id2',
                glpi_tickets.status as 'ticket_status',
                CASE
                    WHEN glpi_tickets.status = 1 THEN '" . Ticket::getStatus(1) . "'
                    WHEN glpi_tickets.status = 2 THEN '" . Ticket::getStatus(2) . "'
                    WHEN glpi_tickets.status = 3 THEN '" . Ticket::getStatus(3) . "'
                    WHEN glpi_tickets.status = 4 THEN '" . Ticket::getStatus(4) . "'
                    WHEN glpi_tickets.status = 5 THEN '" . Ticket::getStatus(5) . "'
                    WHEN glpi_tickets.status = 6 THEN '" . Ticket::getStatus(6) . "'
                    
                    ELSE glpi_tickets.status
                END as 'ticket_status2',
                glpi_tickets.users_id_recipient as 'recipient_userid' ,
                glpi_users.name as 'recipient_username' ,
                glpi_tickets.date as 'ticket_date',
                glpi_tickets.solvedate as 'ticket_solvedate',
                glpi_tickets.closedate as 'ticket_closedate',
                assign_user.users_id as 'assign_user_users_id',
                requester_user.users_id as 'requester_user_users_id',
                observer_user.users_id as 'observer_user_users_id',
                addedfields.belsmunkaszmfield as 'belsmunkaszmfield',
                (SELECT sum(actiontime/3600*cost_time) FROM glpi_ticketcosts WHERE tickets_id=glpi_tickets.id) as 'ticket_sum_time_cost',
                (SELECT sum(cost_fixed) FROM glpi_ticketcosts WHERE tickets_id=glpi_tickets.id) as 'ticket_sum_fix_cost',
                (SELECT sum(cost_material) FROM glpi_ticketcosts WHERE tickets_id=glpi_tickets.id) as 'ticket_sum_material_cost',
                (SELECT sum((actiontime/3600*cost_time)+cost_fixed+cost_material) FROM glpi_ticketcosts WHERE tickets_id=glpi_tickets.id) as 'ticket_sum_cost'
    
            FROM `glpi_tickets` 
                LEFT OUTER JOIN `glpi_users`  ON glpi_users.id = glpi_tickets.users_id_recipient
                LEFT OUTER JOIN `glpi_tickets_users` assign_user ON assign_user.tickets_id = glpi_tickets.id and assign_user.type= " . CommonITILActor::ASSIGN . " 
                LEFT OUTER JOIN `glpi_tickets_users` requester_user ON requester_user.tickets_id = glpi_tickets.id and requester_user.type= " . CommonITILActor::REQUESTER . " 
                LEFT OUTER JOIN `glpi_tickets_users` observer_user ON observer_user.tickets_id = glpi_tickets.id and observer_user.type= " . CommonITILActor::OBSERVER . " 
                LEFT OUTER JOIN `glpi_plugin_fields_ticketiisticketaddons` addedfields ON addedfields.items_id = glpi_tickets.id and addedfields.itemtype= 'Ticket' 
            Where 1=1 
            " .
        getEntitiesRestrictRequest(' AND ', 'glpi_tickets') .
        $report->addSqlCriteriasRestriction('AND');

    if ($filterWithClosed->getParameterValue() == 0) {
        $query .= " and glpi_tickets.status<>6 ";
    } else {
    }

    $query .= $report->getOrderBy('glpi_tickets.date');

    $report->setSqlRequest($query);
    //echo $query;
    $report->execute(['withmassiveaction' => 'Ticket']);
} else {
    Html::Footer();
}
