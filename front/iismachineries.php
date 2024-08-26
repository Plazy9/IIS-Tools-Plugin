<?php
use GlpiPlugin\Iistools\iisMachineries;
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
if (iisMachineries::canView()) {
   //View is granted: display the list.

   //Add page header
   Html::header(iisMachineries::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisMachineries::class, 'iismachineries');

   Search::show(iisMachineries::class);

   Html::footer();
} else {
   //View is not granted.
   Html::displayRightError();
}