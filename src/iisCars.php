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
      echo "showformasdf";
      global $CFG_GLPI;

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      if (!isset($options['display'])) {
         //display per default
         $options['display'] = true;
      }

      $params = $options;
      //do not display called elements per default; they'll be displayed or returned here
      $params['display'] = false;

      $out = '<tr>';
      $out .= '<th>' . __('My label', 'iistools') . '</th>';

      $objectName = autoName(
         $this->fields["name"],
         "name",
         (isset($options['withtemplate']) && $options['withtemplate']==2),
         $this->getType(),
         $this->fields["entities_id"]
      );

      $out .= '<td>';
      $out .= Html::autocompletionTextField(
         $this,
         'name',
         [
            'value'     => $objectName,
            'display'   => false
         ]
      );
      $out .= '</td>';

      $out .= $this->showFormButtons($params);

      if ($options['display'] == true) {
         echo $out;
      } else {
         return $out;
      }
      return true;
   }

   

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