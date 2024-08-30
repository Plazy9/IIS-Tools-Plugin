<?php
namespace GlpiPlugin\Iistools;

use Session;
use CommonDBTM;

use Glpi\Application\View\TemplateRenderer;

class iisCars extends CommonDBTM {

    public static $rightname = 'plugin_iistools';

    
    public function test() {
        //error_log("pali palitest");
        return "pali iis test";
    }

    public function getFuelType() {
        //error_log("pali palitest");'Petrol', 'Diesel', 'Hybrid', 'Electric', 'LPG'
        return ["Petrol"=>__('Petrol', 'iistools'),
                "Diesel"=>__('Diesel', 'iistools'),
                "Hybrid"=>__('Hybrid', 'iistools'),
                "Electric"=>__('Electric', 'iistools'),
                "LPG"=>__('LPG', 'iistools'),
                ];
    }
    
    public function showForm($ID, array $options = []) {
        $twig = TemplateRenderer::getInstance();
        $twig->display('@iistools/iiscars_form.html.twig', [
            'item'             => $this,
            'fueltype'          => self::getFuelType(),
        ]);
        return true;
    }

    public function rawSearchOptions(){ 
       
        //$tab = parent::rawSearchOptions();
        $tab = [];
       // $tab = array_merge($tab, Location::rawSearchOptionsToAdd());
    
        $tab[] = [
            'id'                 => 1,
            'table'              => $this->getTable(),
            'field'              => 'license_plate',
            'name'               => __('License plate', 'iistools'),
            'datatype'         =>  $this->getType(),
            'massiveaction'    => false,
        ];
        
        $tab[] = [
            'id'                 => 2,
            'table'              => $this->getTable(),
            'field'              => 'brand',
            'name'               => __('Car brand', 'iistools'),
            'datatype'         => $this->getType(),
        ];

        $tab[] = [
            'id'                 => 3,
            'table'              => $this->getTable(),
            'field'              => 'type',
            'name'               => __('Car type', 'iistools'),
            'datatype'         => $this->getType(),
            'massiveaction'    => false,
        ];
        $tab[] = [
            'id'                 => 4,
            'table'              => $this->getTable(),
            'field'              => 'key_count',
            'name'               => __('Car key count', 'iistools'),
            'datatype'         => 'number',
            'massiveaction'    => false,
        ];
/*

        $tab[] = [
            'id'                 => 5,
            'table'              => $this->getTable(),
            'field'              => 'primary_driver',
            'name'               => __('primary_driver', 'iistools'),
            'searchtype'  => 'equals',
            'datatype'    => 'specific',
            'massiveaction'    => false,

        ];*/

         $tab[] = [
        'id'                 => 5,
        'table'              => $this->getTable(), 
        'field'              => 'primary_driver',
        'name'               => __('Primary driver id', 'iistools'),
        'datatype'           => 'dropdown',
        
        'massiveaction'      => false,
    ];

    // Kapcsolódó mezők a users táblából
    $tab[] = [
        'id'                 => 6,
        'table'              => 'glpi_users', // Users tábla
        'field'              => 'name', // Felhasználó neve
        'name'               => __('Primary driver name', 'iistools').'',
        'datatype'           => 'itemlink',
        'massiveaction'      => false,
        'linkfield'         => 'primary_driver',
        'joinparams'         => [
            'beforejoin'         => [
                'table'              => $this->getTable(),
                'joinparams'         => [
                    'jointype'           => 'itemtype_item',
                    'specific_itemtype'  => 'iisCars'
                ]
            ]
        ]

    ];
        
        return $tab;
    }

    public function defineTabs($options = []){
        $ong = [];
        $this->addDefaultFormTab($ong);
        $this->addImpactTab($ong, $options);
        $this->addStandardTab('Ticket', $ong, $options);
        return $ong;
    }
    
    static function getMenuName() {
        return __('IIS plugin Cars', 'iistools');
     }
    public static function getTypeName($nb = 0)
    {
        return __('IIS Tools Cars', 'iistools', $nb);
    }

    public static function getIcon()
    {
        return "ti ti-device-laptop";
    }   
}