<?php

namespace GlpiPlugin\Iistools;

use CommonDBTM;
use Html;


class iisCostReport extends CommonDBTM
{

    public static $rightname = 'plugin_iistoolsMachineries';
    
    static function getTable($classname = null) {
        return "glpi_tickettasks";
    }

    function rawSearchOptions() {
        $tab = [];
       // $tab = array_merge($tab, Location::rawSearchOptionsToAdd());
    
        $tab[] = [
            'id'                 => 1,
            'table'              => $this->getTable(),
            'field'              => 'id',
            'name'               => __('ID', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'number'
        ];

        $tab[] = [
            'id'                 => 2,
            'table'              => $this->getTable(),
            'field'              => 'content',
            'name'               => __('Task content', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'specific'
        ];
        $tab[] = [
            'id'                 => 3,
            'table'              => $this->getTable(),
            'field'              => 'date',
            'name'               => __('Task create date', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'date'
        ];

        $tab[] = [
            'id'                 => 31,
            'table'              => $this->getTable(),
            'field'              => 'users_id',
            'name'               => __('Task creator id', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'itemlink'
        ];

        $tab[] = [
            'id'                 => 32,
            'table'              => 'glpi_users',
            'field'              => 'name',
            'name'               => __('Task creator', 'iistools'),
            'massiveaction'      => false,
        ];

        $tab[] = [
            'id'                 => 4,
            'table'              => 'iis_tickets_table', 
            'field'              => 'id', 
            'name'               => __('Ticket id', 'iistools').'',
            'datatype'           => 'number',
            'massiveaction'      => false,
            
            
        ];

        $tab[] = [
            'id'                 => 5,
            'table'              => 'iis_tickets_table', 
            'field'              => 'name', 
            'name'               => __('Ticket', 'iistools').'',
            'datatype'           => 'string',
            'massiveaction'      => false,
            
        ];

        $tab[] = [
            'id'                 => 6,
            'table'              => 'iis_problems_table', 
            'field'              => 'problems_id', 
            'name'               => __('Problem id', 'iistools').'',
            'datatype'           => 'number',
            'massiveaction'      => false,
        ];


        $tab[] = [
            'id'                 => 7,
            'table'              => 'glpi_problems', 
            'field'              => 'name', 
            'name'               => __('Problem', 'iistools').'',
            'datatype'           => 'itemlink',
            'massiveaction'      => false,
            'linkfield'         => 'problems_id',
            'joinparams'         => [
                'beforejoin'         => [
                    'table'              => 'iis_problems_table',
                ]
            ]
        ];

        $tab[] = [
            'id'                 => 8,
            'table'              => 'iis_tickets_table', 
            'field'              => 'entities_id', 
            'name'               => __('Entity id', 'iistools').'',
            'datatype'           => 'number',
            'massiveaction'      => false,
            
            
        ];

        $tab[] = [
            'id'                 => 9,
            'table'              => 'iis_entities_table', 
            'field'              => 'name', 
            'name'               => __('Entity', 'iistools').'',
            'datatype'           => 'string',
            'massiveaction'      => false,
        ];

        $tab[] = [
            'id'                 => 10,
            'table'              => $this->getTable(),
            'field'              => 'actiontime',
            'name'               => __('Ticket actiontime', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'specific'
            
        ];

        $tab[] = [
            'id'                 => 11,
            'table'              => 'iis_ticketcost_table',
            'field'              => 'cost_time',
            'name'               => __('Ticket cost', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'specific',
            
        ];

        $tab[] = [
            'id'                 => 12,
            'table'              => 'iis_ticketcost_table',
            'field'              => 'cost_fixed',
            'name'               => __('Ticket fixed cost', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'specific',
            
        ];


        $tab[] = [
            'id'                 => 13,
            'table'              => 'iis_ticketcost_table',
            'field'              => 'cost_material',
            'name'               => __('Ticket material cost', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'specific',
            
        ];

        return $tab;
    }

    static function getMenuName()
    {
        return __('IIS plugin Cost report', 'iistools');
    }
    public static function getTypeName($nb = 0)
    {
        return __('IIS Cost report', 'iistools', $nb);
    }

    public static function getIcon()
    {
        return "fa-fw ti ti-stack";
    }

    public static function canCreate()
    {
        return true;
    }

    public function canCreateItem()
    {
        return true;
    }
}
