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

    public function showForm($ID, array $options = []) {
      //echo "showformasdf";
      
      $twig = TemplateRenderer::getInstance();
        $twig->display('@iistools/iiscars_form.html.twig', [
            'item'             => $this,
            
        ]);
        return true;
    }

    



    function showFormx($ID, array $options = []) {
        global $CFG_GLPI;
       
        $this->initForm($ID, $options);
        $this->showFormHeader($options);

       
        $userOptions = [];
        $users = User::getTypeName(1);

        foreach ($users as $user) {
            $userOptions[$user['id']] = $user['name'];
        }
    
         $userOptions[1] = 'name';
          $userOptions[2] = 'name2';

        echo "<tr class='tab_bg_1'>";

        echo "<td>" . __('ID') . "</td>";
        echo "<td>";
        echo $ID;
        echo "</td>";

      $this->showFormButtons($options);
 $form->show();
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

                

        $tab[] = [
            'id'                 => 5,
            'table'              => 'glpi_plugin_iistools_iiscars',
            'field'              => 'primary_driver',
            'name'               => __('primary_driver', 'iistools'),
            'datatype'         => $this->getType(),
            'massiveaction'    => false,

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