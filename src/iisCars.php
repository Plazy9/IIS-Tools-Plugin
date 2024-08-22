<?php
namespace GlpiPlugin\Iistools;

use Glpi\Application\View\TemplateRenderer;

use Glpi\Socket;
use CommonDBTM;
use CommonGLPI;
use Computer;
use Html;
use Log;
use MassiveAction;
use Session;
use User;


class iisCars extends CommonDBTM {

    public static $rightname = 'plugin_iistools';

    
    public function test() {
        //error_log("pali palitest");
        return "pali iis test";
    }

    public function getFuelType() {
        //error_log("pali palitest");'Petrol', 'Diesel', 'Hybrid', 'Electric', 'LPG'
        return ["Petrol"=> "Petrol",
                "Diesel"=>"Diesel",
                ];
    }
    
    public function showForm($ID, array $options = []) {
        //echo "showformasdf";
        
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
            'table'              => 'glpi_plugin_iistools_iiscars',
            'field'              => 'license_plate',
            'name'               => __('Car license plate', 'iistools'),
            'datatype'         =>  $this->getType(),
            'massiveaction'    => false,
        ];
        
        $tab[] = [
            'id'                 => 2,
            'table'              => 'glpi_plugin_iistools_iiscars',
            'field'              => 'brand',
            'name'               => __('Car brand', 'iistools'),
            'datatype'         => $this->getType(),
        ];

        $tab[] = [
            'id'                 => 3,
            'table'              => 'glpi_plugin_iistools_iiscars',
            'field'              => 'type',
            'name'               => __('Car type', 'iistools'),
            'datatype'         => $this->getType(),
            'massiveaction'    => false,
        ];
        $tab[] = [
            'id'                 => 4,
            'table'              => 'glpi_plugin_iistools_iiscars',
            'field'              => 'key_count',
            'name'               => __('Car key count', 'iistools'),
            'datatype'         => 'number',
            'massiveaction'    => false,
        ];
/*

        $tab[] = [
            'id'                 => 5,
            'table'              => 'glpi_plugin_iistools_iiscars',
            'field'              => 'primary_driver',
            'name'               => __('primary_driver', 'iistools'),
            'searchtype'  => 'equals',
            'datatype'    => 'specific',
            'massiveaction'    => false,

        ];*/

         $tab[] = [
        'id'                 => 5,
        'table'              => 'glpi_plugin_iistools_iiscars', 
        'field'              => 'primary_driver',
        'name'               => __('Primary Driver ID'),
        'datatype'           => 'dropdown',
        
        'massiveaction'      => false,
    ];

    // Kapcsolódó mezők a users táblából
    $tab[] = [
        'id'                 => 6,
        'table'              => 'glpi_users', // Users tábla
        'field'              => 'name', // Felhasználó neve
        'name'               => __('Primary Driver Name'),
        'datatype'           => 'itemlink',
        'massiveaction'      => false,
        'linkfield'         => 'primary_driver',
        'joinparams'         => [
            'beforejoin'         => [
                'table'              => 'glpi_plugin_iistools_iiscars',
                'joinparams'         => [
                    'jointype'           => 'itemtype_item',
                    'specific_itemtype'  => 'iisCars'
                ]
            ]
        ]

    ];
        
        return $tab;
    }

    

   /*

    function getSearchOptionsNew() {
        global $DB;
       $tab = [];

       $tab[] = [
          'id'                 => 'common',
          'name'               => __('Characteristics')
       ];

       $tab[] = [
          'id'                 => '1',
          'table'              => self::getTable(),
          'field'              => 'type',
          'name'               => __('Car type'),
          'datatype'           => 'itemlink',
          'massiveaction'      => false
       ];



       return $tab;
    }

*/
    static function getMenuName() {
        return __('IIS plugin');
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