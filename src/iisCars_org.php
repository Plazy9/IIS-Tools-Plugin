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


class iisCars extends CommonDBTM {

    public static $rightname = 'plugin_iistools';

    public function showForm($ID, array $options = []) {
      //echo "showformasdf";
      
      $twig = TemplateRenderer::getInstance();
        $twig->display('@iistools/iiscars_form.html.twig', [
            'item'             => $this,
            
        ]);
        return true;
    }

    function getSearchOptionsNew() {
        global $DB;
        error_log("Search options: " . print_r("XXXXX2", true));
       $tab = [];

       $tab[] = [
          'id'                 => 'iisCars',
          'name'               => __('Characteristics')
       ];
       $tab[] = [
            'id'                 => '1',
            'table'              => self::getTable(),
            'field'              => 'id',
            'name'               => _x('quantity', 'Number of printers'),
            'forcegroupby'       => true,
            'usehaving'          => true,
            'massiveaction'      => false
            
        ];
        
       $tab[] = [
          'id'                 => '2',
          'table'              => self::getTable(),
          'field'              => 'type',
          'name'               => __('Car type'),
          'datatype'           => 'itemlink',
          'massiveaction'      => false
       ];

       return $tab;
    }

    public function rawSearchOptions()
    {
        $tab = parent::rawSearchOptions();

        $tab[] = [
            'id'                 => '2',
            'table'              => $this->getTable(),
            'field'              => 'id',
            'name'               => __('ID'),
            'massiveaction'      => false,
            'datatype'           => 'number'
        ];

        $tab = array_merge($tab, Location::rawSearchOptionsToAdd());

        $tab[] = [
            'id'                 => '4',
            'table'              => $this->getTable(),
            'field'              => 'name',
            'name'               => _n('Type', 'Types', 1),
            'datatype'           => 'dropdown'
        ];

        $tab[] = [
            'id'                 => '40',
            'table'              => $this->getTable(),
            'field'              => 'name',
            'name'               => _n('Model', 'iistools', 1),
            'datatype'           => 'dropdown'
        ];

        return $tab;
    }

    public static function getSearchOptions() {
        error_log("Search options: " . print_r("XXXXX3", true));
        $tab = [];

        $tab['common'] = __('Cars', 'iistools');

        $tab[1]['table']     = self::getTable();
        $tab[1]['field']     = 'type';
        $tab[1]['name']      = __('Type', 'iistools');
        $tab[1]['datatype']  = 'dropdown';

        $tab[2]['table']     = self::getTable();
        $tab[2]['field']     = 'name';
        $tab[2]['name']      = __('Name', 'iispiistoolslugin');
        $tab[2]['datatype']  = 'itemlink';

        $tab[3]['table']     = self::getTable();
        $tab[3]['field']     = 'color';
        $tab[3]['name']      = __('Color', 'iistools');
        $tab[3]['datatype']  = 'text';

        return $tab;
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        error_log("Search options: " . print_r("XXXXX4", true));
        echo "XXxxXX".$item->getType();
       
        switch ($item::getType()) {
            case __CLASS__:
                return __('My plugin IIS', 'iistools');
                break;
        }
        return 'ccc';
    }

/*
    public function rawSearchOptions()
    {

        $tab = [];

        $tab[] = [
            'id'               => 1,
            'table'            => $this->getTable(),
            'field'            => 'type',
            'name'             => __('Name', 'iistools'),
            'datatype'         => 'itemlink',
            'itemlink_type'    => $this->getType(),
            'massiveaction'    => false,
        ];

        

        return $tab;
    }
*/
/*


    function defineTabs($options=array()) {
        $ong = array();
        //add main tab for current object
        $this->addDefaultFormTab($ong);
        //add core Document tab
        $this->addStandardTab(__('Document IIS'), $ong, $options);
        return $ong;
    }


    /**
     * Définition du nom de l'onglet
    **/
    /*
    

    
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