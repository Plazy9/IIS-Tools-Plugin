<?php
namespace GlpiPlugin\Iistools;

use Glpi\Application\View\TemplateRenderer;
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
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
        switch ($item::getType()) {
            case __CLASS__:
                return __('My plugin IIS', 'iistools');
                break;
        }
        return '';
    }

    
*/

    static function getMenuName() {
        return __('IIS plugin');
     }
    public static function getTypeName($nb = 0)
    {
        return _n('IIS Tools Cars', 'iistools', $nb);
    }

    public static function getIcon()
    {
        return "ti ti-device-laptop";
    }
}