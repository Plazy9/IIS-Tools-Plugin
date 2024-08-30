<?php

use Glpi\Plugin\Hooks;
use GlpiPlugin\Iistools\iisCars;
use GlpiPlugin\Iistools\iisCameras;
use GlpiPlugin\Iistools\iisMachineries;


define('PLUGIN_IISTOOLS_VERSION', '0.0.1');
define('PLUGIN_IISTOOLS_MIN_GLPI', '10.0.0');
define('PLUGIN_IISTOOLS_MAX_GLPI', '10.0.99');


function plugin_init_iistools() {
    global $PLUGIN_HOOKS, $CFG_GLPI;
    //print_r($PLUGIN_HOOKS);
    $PLUGIN_HOOKS['csrf_compliant']['iistools'] = true;

    $PLUGIN_HOOKS['item_add']['iistools'] = ['Ticket'];
    $PLUGIN_HOOKS['post_init']['iistools'] = 'plugin_iistools_postinit';
    
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
        $PLUGIN_HOOKS['menu_toadd']['iistools'] = ['iisPlugins' => [iisCars::class, 
                                                                 iisMachineries::class,
                                                                 iisCameras::class],
                                                    //'assets'   => iisCars::class,
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
