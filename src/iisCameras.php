<?php
namespace GlpiPlugin\Iistools;

use Glpi\Application\View\TemplateRenderer;

use CommonDBTM;
use Entity;
use Document;
use Document_Item;

class iisCameras extends CommonDBTM {

    public static $rightname = 'plugin_iistoolsCameras';

    public function showForm($ID, array $options = []) {
        global $CFG_GLPI, $DB;
        //error_log("pali palitest");
        
        $document_list = plugin_iistools_getImageList($this);
        
        $twig = TemplateRenderer::getInstance();
        $twig->display('@iistools/iiscameras_form.html.twig', [
            'item'             => $this,
            'documents'            =>$document_list,
        ]);
        return true;
    }

    public function rawSearchOptions(){ 
       
        //$tab = parent::rawSearchOptions();
        $tab = [];

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
            'field'              => 'name',
            'name'               => __('Camera name', 'iistools'),
            'datatype'         =>  $this->getType(),
            'massiveaction'    => false,
        ];
        
        $tab[] = [
            'id'                 => 3,
            'table'              => $this->getTable(),
            'field'              => 'ip',
            'name'               => __('IP address', 'iistools'),
            'datatype'         => $this->getType(),
        ];
        $tab[] = [
            'id'                 => 42,
            'table'              => $this->getTable(),
            'field'              => 'commissioning_date',
            'name'               => __('Camera Start date', 'iistools'),
            'datatype'         => 'date',
            'massiveaction'    => false,
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
        $this->addStandardTab('ManualLink', $ong, $options);
        //$this->addStandardTab('Log', $ong, $options);
        return $ong;
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