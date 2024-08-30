<?php
namespace GlpiPlugin\Iistools;

use Glpi\Application\View\TemplateRenderer;

use Glpi\Socket;
use CommonDBTM;

use Document;
use Document_Item;


class iisMachineries extends CommonDBTM {

    public static $rightname = 'plugin_iistools';

    public function showForm($ID, array $options = []) {
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
        $twig->display('@iistools/iismachineries_form.html.twig', [
            'item'             => $this,
            'documents'            =>$document_list,
        
        ]);
        return true;
    }

    public function rawSearchOptions(){ 
       
        $tab = parent::rawSearchOptions();
        $tab[] = [
            'id'                 => '1',
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
            'name'               => __('Machinery name', 'iistools'),
            'datatype'         =>  $this->getType(),
            'massiveaction'    => false,
        ];
        
        $tab[] = [
            'id'                 => 3,
            'table'              => $this->getTable(),
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
        $this->addStandardTab('Ticket', $ong, $options);
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

    public static function canCreate() {
        return true;
    }

    public function canCreateItem()
    {
        return true;
    }

}