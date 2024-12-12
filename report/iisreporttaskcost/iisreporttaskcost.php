<?php

$USEDBREPLICATE         = 0;
$DBCONNECTION_REQUIRED  = 0;

include("../../../../inc/includes.php");

//TRANS: The name of the report = Users with no right
$report = new PluginReportsAutoReport(__('iisreport_report_title', 'iistools'));

include("../../inc/IisEntityCriteria.php");

new IisReportsEntityCriteria($report, 'glpi_tickets.entities_id', __('Entity'));


//new PluginReportsTextCriteria($report, 'requester_user.users_id', __('Igénylő'));
new PluginReportsUserCriteria($report, 'requester_user.users_id', __('Requester user', 'iistools'));
new PluginReportsUserCriteria($report, 'assign_user.users_id', __('Assign user', 'iistools'));
//new PluginReportsUserCriteria($report, 'observer_user.users_id', __('Observer user', 'iistools'));

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

        /*
        new PluginReportsColumnLink('entities_id', __('Entity'), 'Entity', ['sorton' => 'entities_id']),
        new PluginReportsColumnDate('tickettasks_date', __('Task create date', 'iistools'), ['sorton' => 'tickettasks_date']),
        new PluginReportsColumnLink('ticket_name', __('Ticket', 'iistools'), 'Ticket'),
        new PluginReportsColumnHtml('tickettasks_content', __('Task content', 'iistools'), [
            'with_comment' => true,
            'with_navigate' => true,
            'sorton' => 'tickettasks_id'
        ]),

        new PluginReportsColumnLink('id2', __('Ticket', 'iistools'), 'Ticket', [
            'with_comment' => true,
            'with_navigate' => true
        ]),
*/
        //new PluginReportsColumn('ticket_status', __('Ticket status'), ['sorton' => 'ticket_status']),
        //new PluginReportsColumn('ticket_status2', __('Ticket status', 'iistools')),

        /*
        new PluginReportsColumn('recipient_userid', __('User id'), 'User', [
            'with_comment' => true,
            'with_navigate' => true
        ]),
        new PluginReportsColumnLink('recipient_userid', __('User'), 'User'),
        */


        //new PluginReportsColumnLink('entities_completename', __('Entities'), 'Entity'),


        //new PluginReportsColumnLink('assign_user_users_id', __('Assign user', 'iistools'), 'User'),

        //new PluginReportsColumnLink('requester_user_users_id', __('Requester user', 'iistools'), 'User'),
        //new PluginReportsColumnLink('observer_user_users_id', __('Observer user', 'iistools'), 'User'),

        //new PluginReportsColumn('recipient_username', __('Recipient_user')),

        // new PluginReportsColumn('name', __('Login'), ['sorton' => 'name']),
        //new PluginReportsColumn('ticket_name', __('Ticket name', 'iistools')),
        //new PluginReportsColumn('belsmunkaszmfield', __('Belső munkaszám', 'iistools'), ['sorton' => 'belsmunkaszmfield']),
        //new PluginReportsColumnDate('ticket_date', __('Ticket create date', 'iistools'), ['sorton' => 'ticket_date']),
        //new PluginReportsColumnDate('ticket_solvedate', __('Ticket solve date', 'iistools'), ['sorton' => 'ticket_solvedate']),

        //new PluginReportsColumnDate('ticket_closedate', __('Ticket close date', 'iistools'), ['sorton' => 'ticket_closedate']),

        /*

        

        new PluginReportsColumnInteger('ticket_sum_tasknum', __('Num of ticket Tasks', 'iistools'), ['sorton' => 'ticket_sum_tasknum']),
        new PluginReportsColumnFloat('ticket_sum_time_cost', __('Ticket sum cost of time costs', 'iistools'), ['sorton' => 'ticket_sum_time_cost']),
        new PluginReportsColumnFloat('ticket_sum_fix_cost', __('Ticket sum cost of fixed costs', 'iistools'), ['sorton' => 'ticket_sum_fix_cost']),
        new PluginReportsColumnFloat('ticket_sum_material_cost', __('Ticket sum cost of material cost', 'iistools'), ['sorton' => 'ticket_sum_material_cost']),
        new PluginReportsColumnFloat('ticket_sum_cost', __('Ticket sum cost', 'iistools'), ['sorton' => 'ticket_sum_cost']),
*/

        new PluginReportsColumnInteger('tickettasks_id', __('Task id', 'iistools'),  ['sorton' => 'tickettasks_id']),

        new PluginReportsColumnHtml('tickettasks_content', __('Task content', 'iistools'), [
            'with_comment' => true,
            'with_navigate' => true,
            'sorton' => 'tickettasks_content'
        ]),

        new PluginReportsColumnDate('tickettasks_date', __('Task create date', 'iistools'), ['sorton' => 'tickettasks_date']),
        new PluginReportsColumnInteger('ticket_id', __('Ticket id', 'iistools')),
        new PluginReportsColumnLink('ticket_name', __('Ticket', 'iistools'), 'Ticket'),
        new PluginReportsColumnInteger('problem_id', __('Problem id', 'iistools')),
        new PluginReportsColumnLink('problem_name', __('Problem', 'iistools'), 'Problem'),

        new PluginReportsColumnInteger('task_creator_id', __('Task creator id', 'iistools'), ['sorton' => 'task_creator_id']),
        new PluginReportsColumnLink('task_creator_name', __('Task creator name', 'iistools'), 'User', ['sorton' => 'task_creator_name']),

        new PluginReportsColumnInteger('entities_id', __('Entity id', 'iistools'), ['sorton' => 'entities_id']),
        new PluginReportsColumnLink('entities_name', __('Entity', 'iistools'), 'Entity', ['sorton' => 'entities_name']),

        new PluginReportsColumnFloat('ticket_actiontime', __('Ticket actiontime', 'iistools'), ['sorton' => 'ticket_actiontime']),
        new PluginReportsColumnFloat('ticket_cost', __('Ticket cost', 'iistools'), ['sorton' => 'ticket_cost']),
        new PluginReportsColumnFloat('ticket_cost_fixed', __('Ticket fixed cost', 'iistools'), ['sorton' => 'ticket_cost_fixed']),
        new PluginReportsColumnFloat('ticket_cost_material', __('Ticket material cost', 'iistools'), ['sorton' => 'ticket_cost_material']),

    ];

    $report->setColumns($cols);

    $query =    "Select glpi_tickettasks.id as 'tickettasks_id', 
                    glpi_tickettasks.content as 'tickettasks_content',
                    date_format(glpi_tickettasks.date, '%Y-%m-%d') as 'tickettasks_date', 
                    glpi_tickets.id  as 'ticket_id',
                    glpi_tickets.name as 'ticket_name', 
                    (SELECT problems_id FROM glpi_problems_tickets WHERE tickets_id=glpi_tickets.id) as 'problem_id',
                    (SELECT name FROM glpi_problems WHERE id=(SELECT problems_id FROM glpi_problems_tickets WHERE tickets_id=glpi_tickets.id)) as 'problem_name',
                    glpi_tickettasks.users_id as 'task_creator_id',
                    (SELECT name FROM glpi_users WHERE id=glpi_tickettasks.users_id ) as 'task_creator_name',
                    glpi_tickets.entities_id as 'entities_id', 
                    (SELECT completename FROM glpi_entities WHERE id=glpi_tickets.entities_id) as 'entities_name',
                    glpi_tickettasks.actiontime/3600 as 'ticket_actiontime',
                    (SELECT actiontime/3600*cost_time FROM glpi_ticketcosts WHERE SUBSTRING_INDEX(glpi_ticketcosts.name , '_', 1)=glpi_tickettasks.id ) as 'ticket_cost', 
                    (SELECT cost_fixed FROM glpi_ticketcosts WHERE SUBSTRING_INDEX(glpi_ticketcosts.name , '_', 1)=glpi_tickettasks.id ) as 'ticket_cost_fixed',
                    (SELECT cost_material FROM glpi_ticketcosts WHERE SUBSTRING_INDEX(glpi_ticketcosts.name , '_', 1)=glpi_tickettasks.id ) as 'ticket_cost_material'
                FROM glpi_tickettasks 
                    LEFT OUTER JOIN `glpi_tickets` ON glpi_tickets.id = glpi_tickettasks.tickets_id 
                    LEFT OUTER JOIN `glpi_users` ON glpi_users.id = glpi_tickets.users_id_recipient 
                    LEFT OUTER JOIN `glpi_entities` ticketentities ON ticketentities.id = glpi_tickets.entities_id 
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
