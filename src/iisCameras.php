<?php
namespace GlpiPlugin\Iistools;

use Glpi\Application\View\TemplateRenderer;

use CommonDBTM;
use Document;
use Document_Item;

class iisCameras extends CommonDBTM {

    public static $rightname = 'plugin_iistools';

    public function showForm($ID, array $options = []) {
        //error_log("pali palitest");
        echo "asdf";
        echo $this->getType();
        $_SESSION["glpiactiveprofile"]["helpdesk_item_type"][]=$this->getType();
        print_r($_SESSION["glpiactiveprofile"]["helpdesk_item_type"]);
        global $CFG_GLPI, $DB;
        $document_list = [];
        $document = new Document_Item();
        
        $documents = $document->find([
            'itemtype' => $this->getType(),
            'items_id' => $this->getID()
        ]);
        
        foreach ($documents as $document_id => $document_data) {
            $doc = new Document();
            $doc->getFromDB($document_data['documents_id']);

            if (strpos($doc->fields['mime'], 'image') === 0) {
                
                $document_list[] = [
                    'download_url' => $CFG_GLPI["root_doc"] . "/front/document.send.php?docid=".$document_data['documents_id'],
                    'mime'         => $doc->fields['mime']
                ];
            }
        }

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

    public function defineTabs($options = []){
        $ong = [];
        $this->addDefaultFormTab($ong);
        $this->addImpactTab($ong, $options);
        $this->addStandardTab('Ticket', $ong, $options);
        $this->addStandardTab('Document_Item', $ong, $options);
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