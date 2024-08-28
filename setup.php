<?php

use Glpi\Plugin\Hooks;
use GlpiPlugin\Iistools\iisCars;
use GlpiPlugin\Iistools\iisCameras;
use GlpiPlugin\Iistools\iisMachineries;


define('PLUGIN_IISTOOLS_VERSION', '0.0.1');
define('PLUGIN_IISTOOLS_MIN_GLPI', '10.0.0');
define('PLUGIN_IISTOOLS_MAX_GLPI', '10.0.99');


function plugin_init_iistools() {
    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['csrf_compliant']['iistools'] = true;

    Plugin::registerClass('PluginIistoolsProfile', ['addtabon' => 'Profile']);


    if (iisCars::canView()) { // Right set in change_profile hook
        $PLUGIN_HOOKS['menu_toadd']['iistools'] = ['plugins' => [iisCars::class, 
                                                                 iisMachineries::class,
                                                                 iisCameras::class],
                                                                /*'tools'   => iisCars::class*/
                                                            ];

    }
    //$PLUGIN_HOOKS['config_page']['iistools'] = 'front/iiscars.php';
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
