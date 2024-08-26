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


class iisMachineries extends CommonDBTM {

    public static $rightname = 'plugin_iistools';
    public static $table_name= "glpi_plugin_iistools_iismachineries";

    
    public function showForm($ID, array $options = []) {
        $twig = TemplateRenderer::getInstance();
        $twig->display('@iistools/iismachineries_form.html.twig', [
            'item'             => $this,
        
        ]);
        return true;
    }

    public function rawSearchOptions(){ 
       
        //$tab = parent::rawSearchOptions();
        $tab = [];
       // $tab = array_merge($tab, Location::rawSearchOptionsToAdd());
    
        $tab[] = [
            'id'                 => 2,
            'table'              => self::$table_name,
            'field'              => 'name',
            'name'               => __('Machinery name', 'iistools'),
            'datatype'         =>  $this->getType(),
            'massiveaction'    => false,
        ];
        
        $tab[] = [
            'id'                 => 3,
            'table'              => self::$table_name,
            'field'              => 'type',
            'name'               => __('Machinery type', 'iistools'),
            'datatype'         => $this->getType(),
        ];
        
        return $tab;
    }

    public function getCloneRelations(): array
    {
        return [

            Document_Item::class,
        ];
    }

    public function defineTabs($options = []){
        $ong = [];
        $this->addDefaultFormTab($ong);
        $this->addImpactTab($ong, $options);
        $this->addStandardTab('Document_Item', $ong, $options);
        return $ong;
    }

    static function getMenuName() {
        return __('IIS plugin Machineries', 'iistools');
     }
    public static function getTypeName($nb = 0)
    {
        return __('IIS Tools Machineries', 'iistools', $nb);
    }

    public static function getIcon()
    {
        return "fa-fw ti ti-stack";
    }
}