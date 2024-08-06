<?php


use Glpi\Application\View\TemplateRenderer;
use GlpiPlugin\Iistools\iisCars;


class PluginIistoolsProfile extends Profile
{
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        return self::createTabEntry(
            iisCars::getTypeName(Session::getPluralNumber())
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
        $twig->display("@news/profile.html.twig", [
            'id'      => $item->getID(),
            'profile' => $profile,
            'title'   => iisCars::getTypeName(Session::getPluralNumber()),
            'rights'  => [
                [
                    'itemtype' => iisCars::getType(),
                    'label'    => iisCars::getTypeName(Session::getPluralNumber()),
                    'field'    => iisCars::$rightname,
                ]
            ]
        ]);

        return true;
    }
}
