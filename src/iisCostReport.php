<?php

namespace GlpiPlugin\Iistools;

use CommonDBTM;
use Html;
use Search;


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

        $tab['taskcontent'] = [
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
            'name'               => __('Task creator name', 'iistools'),
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
            'additionalfields'   => ['id', 'name']
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
            'additionalfields'   => ['id', 'name']
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

    static function dashboardTypes() {
        return [
            /*
           'example2' => [
              'label'    => __("Plazy dashboard type1"),
              'function' => iisCostReport::class . "::cardWidget",
              'image'    => "",
           ],
           */
           'example_static' => [
              'label'    => __("Plazy dashboard type2"),
              'function' => iisCostReport::class . "::cardWidgetWithoutProvider",
              'image'    => "",
           ],
           
        ];
     }
  
  
     static function dashboardCards($cards = []) {
        if (is_null($cards)) {
           $cards = [];
        }
        $new_cards =  [
            /*
           'plugin_example_card' => [
              'widgettype'   => ["stackedbars", 'stackedHBars'],
              'label'        => __("IIS Widget time of TicketTask multi"),
              'provider'     => iisCostReport::class . "::cardDataProvider",
           ],
           */
            'plugin_example_card2' => [
              'widgettype'   => ["bar", "pie"],
              'label'        => __("IIS Widget time of TicketTask"),
              'provider'     => iisCostReport::class . "::cardDataProvider_TaskCost",
           ],
           
           'plugin_example_card_without_provider' => [
              'widgettype'   => ["example_static"],
              'label'        => __("IIS Widget"),
           ],
        ];
  
        return array_merge($cards, $new_cards);
     }
     /*
     static function cardWidget(array $params = []) {
        $default = [
           'data'  => [],
           'title' => '',
           // this property is "pretty" mandatory,
           // as it contains the colors selected when adding widget on the grid send
           // without it, your card will be transparent
           'color' => '',
        ];
  
        $p = array_merge($default, $params);
  
        // you need to encapsulate your html in div.card to benefit core style
        $html = "<div class='card' style='background-color: {$p["color"]};'>";
        $html.= "<h2>XXX{$p['title']}</h2>";
        $html.= "SSS<ul>";
        foreach ($p['data'] as $line) {
           $html.= "<li>CCC $line</li>";
        }
        $html.= "</ul>";
        $html.= "</div>";
  
        return $html;
     }
*/
     static function addFilterToParams($params){
        $defaultparams=array();
        if(isset($params['apply_filters']['dates'])){
            $defaultparams['criteria'] =[
                                            [
                                                'field'      => 3,        // taskcreated field index in search options
                                                'searchtype' => 'morethan',  // type of search
                                                'value'      => $params['apply_filters']['dates'][0],         // value to search
                                            ],
                                            [
                                                'field'      => 3,        // taskcreated field index in search options
                                                'searchtype' => 'lessthan',  // type of search
                                                'value'      => $params['apply_filters']['dates'][1],         // value to search
                                            ],
                                        ];
        }
        return $defaultparams;
     }

     static function cardWidgetWithoutProvider(array $params = []) {
        $debug=false;
        
        $sum_value=array();
        $forcedisplay=array(1, 2, 3, 31, 32, 4, 5, 6, 7, 8, 9, 10, 11, 12,13); //all fields
        
        $p = array_merge(self::addFilterToParams($params), $params);

        $data = Search::getDatas(iisCostReport::class, $p, $forcedisplay);
        //var_dump($data['data']['rows']);
        $row_num = 1;
        foreach($data['data']['rows'] as $ResultKey => $ResultRow){
            $sum_value[$ResultRow['GlpiPlugin\Iistools\iisCostReport_4']['displayname']]['value']+=$ResultRow['GlpiPlugin\Iistools\iisCostReport_10'][0]['name'];
            $sum_value[$ResultRow['GlpiPlugin\Iistools\iisCostReport_4']['displayname']]['name']=$ResultRow['GlpiPlugin\Iistools\iisCostReport_5']['displayname'];
            if($debug){
                $item_num = 1;
                $row_num++;
                
                foreach ($data['data']['cols'] as $col) {
                    $colName = $col["name"];
                    $colkey = "{$col['itemtype']}_{$col['id']}";
                    echo $colName."->". $colkey . "->";
                    
                    if($colkey=='GlpiPlugin\Iistools\iisCostReport_10'){
                        $Value=Search::showItem(3,
                                                $ResultRow[$colkey][0]['name'],
                                                $item_num,
                                                $row_num
                                            );
                    }else{
                        $Value=Search::showItem(3,
                                                $ResultRow[$colkey]['displayname'],
                                                $item_num,
                                                $row_num
                                            );
                    }
                    
                    echo $Value; 
                    echo "<br>";
                
                }
                echo "<hr>";
            }
        
        }

        $default = [
            // this property is "pretty" mandatory,
            // as it contains the colors selected when adding widget on the grid send
            // without it, your card will be transparent
            'color' => '',
        ];
        $p = array_merge($default, $params);

        // you need to encapsulate your html in div.card to benefit core style
        //$html = $columnsList;
        print_r($sum_value);
        $html = "teszt";
        return $html;
     }
/*
     static function cardDataProvider(array $params = []) {
        $default_params = [
           'label' => null,
           'icon'  => "fas ",
        ];
        $params = array_merge($default_params, $params);
        $card_data['labels'][]="asdf1";
        $card_data['labels'][]="asdf2";
        $card_data['labels'][]="asdf3";
        $card_data['series'][]=array('name'=> 'izé1', 'data' => [12,30,40]);
        $card_data['series'][]=array('name'=> 'izé3', 'data' => [7,null,30]);
        
        return [
           'label' => $params['label'],
           'icon'  => $params['icon'],
           'data'  => $card_data,
           'legend'=> false,
        ];
     }
*/
    static function TaskCostValue(array $params = []) {
        $card_data=array();
        $sum_value=array();
        $forcedisplay=array(1, 2, 3, 31, 32, 4, 5, 6, 7, 8, 9, 10, 11, 12,13); //all fields

        $p = array_merge(self::addFilterToParams($params), $params);

        $data = Search::getDatas(iisCostReport::class, $p, $forcedisplay);

        foreach($data['data']['rows'] as $ResultKey => $ResultRow){
            $sum_value[$ResultRow['GlpiPlugin\Iistools\iisCostReport_4'][0]['name']]['timevalue']+=$ResultRow['GlpiPlugin\Iistools\iisCostReport_10'][0]['name'];
            $sum_value[$ResultRow['GlpiPlugin\Iistools\iisCostReport_4'][0]['name']]['name']=$ResultRow['GlpiPlugin\Iistools\iisCostReport_5'][0]['name'];
        }

        foreach($sum_value as $key => $value){
            $card_data[] = ['label' => $key." - \"".($value['name'])."\" összesen: ".  Html::timestampToString($value['timevalue']),                
                            'number' => $value['timevalue']/60,
                            'url' => '#'
                            ];
        }
        return $card_data;
    }

     static function cardDataProvider_TaskCost(array $params = []) {
        $default_params = [
           'label' => null,
           'icon'  => "fas ",
        ];
        $params = array_merge($default_params, $params);
        $card_data = self::TaskCostValue($params);

        return [
           'label' => $params['label'],
           'icon'  => $params['icon'],
           'data'  => $card_data,
           'donut' => true,
           'legend'=> false,
        ];
     }
  
}
