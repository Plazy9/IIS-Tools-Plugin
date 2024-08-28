<?php
namespace GlpiPlugin\Iistools;

use Glpi\Application\View\TemplateRenderer;

use CommonDBTM;

class iisCameras extends CommonDBTM {

    public static $rightname = 'plugin_iistools';
/*
    public function showForm($ID, array $options = []) {
        
        $twig = TemplateRenderer::getInstance();
        $twig->display('@iistools/iiscameras_form.html.twig', [
            'item'             => $this,
        ]);
        return true;
    }
*/
    public function rawSearchOptions(){ 
       
        //$tab = parent::rawSearchOptions();
        $tab = [];
       // $tab = array_merge($tab, Location::rawSearchOptionsToAdd());
    
        $tab[] = [
            'id'                 => 1,
            'table'              => $this->getTable(),
            'field'              => 'name',
            'name'               => __('Camera Name', 'iistools'),
            'datatype'         =>  $this->getType(),
            'massiveaction'    => false,
        ];
        
        $tab[] = [
            'id'                 => 2,
            'table'              => $this->getTable(),
            'field'              => 'ip',
            'name'               => __('Camera ip', 'iistools'),
            'datatype'         => $this->getType(),
        ];
        
        return $tab;
    }

    
    static function getMenuName() {
        return __('IIS plugin Cameras', 'iistools');
     }
    public static function getTypeName($nb = 0)
    {
        return __('IIS Tools Cameras', 'iistools', $nb);
    }

    public static function getIcon()
    {
        return "ti ti-device-laptop";
    }
}