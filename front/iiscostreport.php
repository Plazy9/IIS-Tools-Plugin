<?php
use GlpiPlugin\Iistools\iisCostReport;
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
if (iisCostReport::canView()) {
   //View is granted: display the list.

   //Add page header
   Html::header(iisCostReport::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisCostReport::class, 'iisCostReport');
   
   Search::show(iisCostReport::class);
   
   Html::footer();
} else {
   //View is not granted.
   Html::displayRightError();
}