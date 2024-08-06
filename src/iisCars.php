<?php
namespace GlpiPlugin\Iistools;

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
      global $CFG_GLPI;

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";

      echo "<td>" . __('ID') . "</td>";
      echo "<td>";
      echo $ID;
      echo "</td>";

      $this->showFormButtons($options);

      return true;
   }


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
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
        switch ($item::getType()) {
            case __CLASS__:
                return __('My plugin IIS', 'iistools');
                break;
        }
        return '';
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
        switch ($item::getType()) {
            case __CLASS__:
                self::myStaticMethod();
                break;
        }
        return true;
    }


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