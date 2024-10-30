<?php
namespace GlpiPlugin\Iistools;

use Session;
use Entity;
use CommonDBTM;

use Glpi\Application\View\TemplateRenderer;

class iisCars extends CommonDBTM {

    public static $rightname = 'plugin_iistoolsCars';

    
    public function test() {
        //error_log("pali palitest");
        return "pali iis test";
    }

    public function getFuelType() {
        //error_log("pali fueltype XXX");//'Petrol', 'Diesel', 'Hybrid', 'Electric', 'LPG'
        return ["Petrol"=>__('Petrol', 'iistools'),
                "Diesel"=>__('Diesel', 'iistools'),
                "Hybrid"=>__('Hybrid', 'iistools'),
                "Electric"=>__('Electric', 'iistools'),
                "LPG"=>__('LPG', 'iistools'),
                ];
    }
    
    public function showForm($ID, array $options = []) {

        $document_list = plugin_iistools_getImageList($this);

        $twig = TemplateRenderer::getInstance();
        $twig->display('@iistools/iiscars_form.html.twig', [
            'item'             => $this,
            'fueltype'          => self::getFuelType(),
            'documents'            =>$document_list,
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
            'field'              => 'id',
            'name'               => __('ID', 'iistools'),
            'massiveaction'      => false,
            'datatype'           => 'number'
        ];

        $tab[] = [
            'id'                 => 2,
            'table'              => $this->getTable(),
            'field'              => 'license_plate',
            'name'               => __('License plate', 'iistools'),
            'datatype'         =>  $this->getType(),
            'massiveaction'    => false,
        ];
        
        $tab[] = [
            'id'                 => 3,
            'table'              => $this->getTable(),
            'field'              => 'brand',
            'name'               => __('Car brand', 'iistools'),
            'datatype'         => $this->getType(),
        ];

        $tab[] = [
            'id'                 => 4,
            'table'              => $this->getTable(),
            'field'              => 'type',
            'name'               => __('Car type', 'iistools'),
            'datatype'         => $this->getType(),
            'massiveaction'    => false,
        ];
        $tab[] = [
            'id'                 => 5,
            'table'              => $this->getTable(),
            'field'              => 'key_count',
            'name'               => __('Car key count', 'iistools'),
            'datatype'         => 'number',
            'massiveaction'    => false,
        ];
        $tab[] = [
            'id'                 => 41,
            'table'              => $this->getTable(),
            'field'              => 'technical_validity',
            'name'               => __('Technical validity', 'iistools'),
            'datatype'         => 'date',
            'massiveaction'    => false,
        ];

        $tab[] = [
            'id'                 => 42,
            'table'              => $this->getTable(),
            'field'              => 'commissioning_date',
            'name'               => __('Commissioning date', 'iistools'),
            'datatype'         => 'date',
            'massiveaction'    => false,
        ];

        $tab[] = [
            'id'                 => 43,
            'table'              => $this->getTable(),
            'field'              => 'acquisition_date',
            'name'               => __('Acquisition date', 'iistools'),
            'datatype'         => 'date',
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
            'id'                 => 6,
            'table'              => $this->getTable(), 
            'field'              => 'primary_driver',
            'name'               => __('Primary driver id', 'iistools'),
            'datatype'           => 'dropdown',
            
            'massiveaction'      => false,
        ];

        // Kapcsolódó mezők a users táblából
        $tab[] = [
            'id'                 => 7,
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

        $tab[] = [
            'id'                 => 80,
            'table'              => 'glpi_entities',
            'field'              => 'completename',
            'name'               => Entity::getTypeName(1),
            'datatype'           => 'dropdown'
        ];
        
        return $tab;
    }

    public function defineTabs($options = []){

        $ong = [];
        $this->addDefaultFormTab($ong);
        $this->addImpactTab($ong, $options);
        $this->addStandardTab('Ticket', $ong, $options);
        $this->addStandardTab('Document_Item', $ong, $options);
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