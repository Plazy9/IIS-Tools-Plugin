<?php
use GlpiPlugin\Iistools\iisCars;

include ('../../../inc/includes.php');

if ($_SESSION["glpiactiveprofile"]["interface"] == "central") {
   Html::header(iisCars::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisCars::class, 'iiscars');
} else {
   Html::helpHeader("TITRE", $_SERVER['PHP_SELF']);
}

$example = new iisCars();
$example->display($_GET);

Html::footer();

/*
include ('../../../inc/includes.php');

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('iistools') || !$plugin->isActivated('iistools')) {
   Html::displayNotFoundError();
}

$object = new iisCars();

if (isset($_POST['add'])) {
   //Check CREATE ACL
   $object->check(-1, CREATE, $_POST);
   //Do object creation
   $newid = $object->add($_POST);
   //Redirect to newly created object form
   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/front/iiscars.form.php?id=$newid");
} else if (isset($_POST['update'])) {
   //Check UPDATE ACL
   $object->check($_POST['id'], UPDATE);
   //Do object update
   $object->update($_POST);
   //Redirect to object form
   Html::back();
} else if (isset($_POST['delete'])) {
   //Check DELETE ACL
   $object->check($_POST['id'], DELETE);
   //Put object in dustbin
   $object->delete($_POST);
   //Redirect to objects list
   $object->redirectToList();
} else if (isset($_POST['purge'])) {
   //Check PURGE ACL
   $object->check($_POST['id'], PURGE);
   //Do object purge
   $object->delete($_POST, 1);
   //Redirect to objects list
   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/front/iiscars.php");
} else {
   //per default, display object
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : 0);
   $object->display(
      [
         'id'           => $_GET['id'],
         'withtemplate' => $withtemplate
      ]
   );
}
   */