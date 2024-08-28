<?php
use GlpiPlugin\Iistools\iisCameras;
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
if (iisCameras::canView()) {
   //View is granted: display the list.

   //Add page header
   Html::header(iisCameras::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisCameras::class, 'iiscameras');

   Search::show(iisCameras::class);

   Html::footer();
} else {
   //View is not granted.
   Html::displayRightError();
}