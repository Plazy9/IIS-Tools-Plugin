<?php

use Glpi\Plugin\Hooks;
use GlpiPlugin\Iistools\iisCars;
use GlpiPlugin\Iistools\iisCameras;
use GlpiPlugin\Iistools\iisMachineries;


define('PLUGIN_IISTOOLS_VERSION', '0.0.2');
define('PLUGIN_IISTOOLS_MIN_GLPI', '10.0.0');
define('PLUGIN_IISTOOLS_MAX_GLPI', '10.0.99');


function plugin_init_iistools() {
    global $PLUGIN_HOOKS, $CFG_GLPI;
    //print_r($PLUGIN_HOOKS);
    $PLUGIN_HOOKS['csrf_compliant']['iistools'] = true;

    $PLUGIN_HOOKS['item_add']['iistools'] = ['Ticket'];
    $PLUGIN_HOOKS['post_init']['iistools'] = 'plugin_iistools_postinit';
    

    $PLUGIN_HOOKS[Hooks::PRE_ITEM_ADD]['iistools'] = [
        iisCars::class  => 'plugin_iistools_iisCars_validate',
     ];

    $PLUGIN_HOOKS[Hooks::PRE_ITEM_UPDATE]['iistools'] = [
        iisCars::class  => 'plugin_iistools_iisCars_validate',
     ];

    Plugin::registerClass('PluginIistoolsProfile', ['addtabon' => 'Profile']);
    Plugin::registerClass('PluginIisToolsIisCars', ['addtabon' => 'Ticket']);

    $CFG_GLPI["ticket_types"][] = iisCars::class;
    $CFG_GLPI["ticket_types"][] = iisMachineries::class;
    $CFG_GLPI["ticket_types"][] = iisCameras::class;
    $PLUGIN_HOOKS['assign_to_ticket']['iistools'] = 1;

    $_SESSION["glpiactiveprofile"]["helpdesk_item_type"][]=iisCars::getType();
    $_SESSION["glpiactiveprofile"]["helpdesk_item_type"][]=iisMachineries::getType();
    $_SESSION["glpiactiveprofile"]["helpdesk_item_type"][]=iisCameras::getType();


    if (iisCars::canView()) { // Right set in change_profile hook
        $PLUGIN_HOOKS['menu_toadd']['iistools'] = ['plugins' => [iisCars::class, 
                                                                 iisMachineries::class,
                                                                 iisCameras::class],
                                                    'assets' => [iisCars::class, 
                                                                 iisMachineries::class,
                                                                 iisCameras::class],
                                                    ];

    }
    //$PLUGIN_HOOKS['config_page']['iistools'] = 'front/iiscars.php';
}
function plugin_iistools_postinit() {
    // Regisztráljuk az iisCars osztályt az eszközök közé
    Plugin::registerClass('PluginIistoolsIisCars', ['addtabon' => 'Ticket']);
}
function plugin_version_iistools() {
    return [
        'name'           => 'IIS Tools',
        'version'        =>  PLUGIN_IISTOOLS_VERSION,
        'author'         => 'Plazy',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://example.com',
        'requirements'   => [
         'glpi' => [
            'min' => PLUGIN_IISTOOLS_MIN_GLPI,
            'max' => PLUGIN_IISTOOLS_MAX_GLPI,
         ]
      ]
    ];
}

function plugin_iistools_iisCars_validate(CommonDBTM $item){
    //error_log("pali palitest validate XXXXX");
    var_dump($item->fields['technical_validity']);
    if ($item->fields['technical_validity']==''){
        $item->fields['technical_validity']= null;
    }
    if ($item->fields['commissioning_date']==''){
        $item->fields['commissioning_date']= NULL;
    }
    var_dump($item->fields['technical_validity']);
    //exit();
    return true;
}

function plugin_iistools_getImageList($item){
    global $CFG_GLPI;

    $document_list = [];
    $document = new Document_Item();
    
    $documents = $document->find([
        'itemtype' => $item->getType(),
        'items_id' => $item->getID()
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

    return $document_list;
}

