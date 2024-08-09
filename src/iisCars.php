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
        return "pali iis test";
    }
    
    public function showForm($ID, array $options = []) {
        //echo "showformasdf";
        $twig = TemplateRenderer::getInstance();

        $twig->display('@iistools/iiscars_form.html.twig', [
            'item'             => $this,
        ]);
        return true;
    }

    static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = []) {
      error_log("pali xxx SpecificValueToSelect");

      if (!is_array($values)) {
         $values = [$field => $values];
      }
      $options['display'] = false;

      switch ($field) {
         case 'primary_driver' :
            return Dropdown::showFromArray($name, [
               '0' => __('Inactive'),
               '1' => __('Active'),
            ], [
               'value'               => $values[$field],
               'display_emptychoice' => false,
               'display'             => false
            ]);
            break;
      }
      return parent::getSpecificValueToSelect($field, $name, $values, $options);
   }

   public function defineTabs($options = []) {
      $ong = [];
      $this->addDefaultFormTab($ong);
      $this->addStandardTab(self::class, $ong, $options);

      return $ong;
   }

    static function showIisUsers($myname, $options = [])
    {
        error_log("pali showIisUserx");
        
        $values = [];
        if (isset($options['display_emptychoice']) && ($options['display_emptychoice'])) {
            if (isset($options['emptylabel'])) {
                $values[''] = $options['emptylabel'];
            } else {
                $values[''] = self::EMPTY_VALUE;
            }
            unset($options['display_emptychoice']);
        }

        $values = array_merge($values, self::getLanguages());
        return self::showFromArray($myname, $values, $options);
    }

    public static function getLanguages()
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        $languages = [];
        foreach ($CFG_GLPI["languages"] as $key => $val) {
            if (isset($val[1]) && is_file(GLPI_ROOT . "/locales/" . $val[1])) {
                $languages[$key] = "asdf".$val[0];
            }
        }

        return $languages;
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