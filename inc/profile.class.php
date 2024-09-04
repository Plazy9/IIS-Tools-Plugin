<?php


use Glpi\Application\View\TemplateRenderer;
use GlpiPlugin\Iistools\iisCameras;
use GlpiPlugin\Iistools\iisCars;
use GlpiPlugin\Iistools\iisMachineries;

class PluginIistoolsProfile extends Profile
{
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        return self::createTabEntry(
            __("IIS Tools Profiles", "iistools")
        );
    }

    public static function displayTabContentForItem(
        CommonGLPI $item,
        $tabnum = 1,
        $withtemplate = 0
    ) {
        if (!$item instanceof Profile || !self::canView()) {
            return false;
        }

        $profile = new Profile();
        $profile->getFromDB($item->getID());

        $twig = TemplateRenderer::getInstance();
        $twig->display("@iistools/profile.html.twig", [
            'id'      => $item->getID(),
            'profile' => $profile,
            'title'   => __("IIS Profles rights", 'iistools'),
            'rights'  => [
                [
                    'itemtype' => iisCars::getType(),
                    'label'    => iisCars::getTypeName(Session::getPluralNumber()),
                    'field'    => iisCars::$rightname,
                ],
                [
                    'itemtype' => iisCameras::getType(),
                    'label'    => iisCameras::getTypeName(Session::getPluralNumber()),
                    'field'    => iisCameras::$rightname,
                ]
                ,
                [
                    'itemtype' => iisMachineries::getType(),
                    'label'    => iisMachineries::getTypeName(Session::getPluralNumber()),
                    'field'    => iisMachineries::$rightname,
                ]
            ]
        ]);

        return true;
    }
}
