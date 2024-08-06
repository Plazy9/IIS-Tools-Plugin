<?php
use GlpiPlugin\Iistools\iisCars;
use Html;

include ("../../../inc/includes.php");
// TODO:

//Session::checkRight("computer", READ);

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('iistools') || !$plugin->isActivated('iistools')) {
   Html::displayNotFoundError();
}

//check for ACLs
if (iisCars::canView()) {
   //View is granted: display the list.

   //Add page header
   Html::header(iisCars::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisCars::class, 'iiscars');

   Search::show(iisCars::class);

   Html::footer();
} else {
   //View is not granted.
   Html::displayRightError();
}