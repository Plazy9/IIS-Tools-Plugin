<?php
use GlpiPlugin\Iistools\iisCars;

include ('../../../inc/includes.php');

$iiscars = new iisCars();

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('iistools') || !$plugin->isActivated('iistools')) {
  Html::displayNotFoundError();
}


Html::header(iisCars::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisCars::class, '');


if (isset($_POST['add'])) {
   //Check CREATE ACL
   $iiscars->check(-1, CREATE, $_POST);
   //Do object creation
   $newid = $iiscars->add($_POST);
   //Redirect to newly created object form
   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/front/iiscars.form.php?id=$newid");
} else if (isset($_POST['update'])) {
   //Check UPDATE ACL
   $iiscars->check($_POST['id'], UPDATE);
   //Do object update
   $iiscars->update($_POST);
   //Redirect to object form
   Html::back();
} else if (isset($_POST['delete'])) {
   //Check DELETE ACL
   $iiscars->check($_POST['id'], DELETE);
   //Put object in dustbin
   $iiscars->delete($_POST);
   //Redirect to objects list
   $iiscars->redirectToList();
} else if (isset($_POST['purge'])) {
   //Check PURGE ACL
   $iiscars->check($_POST['id'], PURGE);
   //Do object purge
   $iiscars->delete($_POST, 1);
   //Redirect to objects list
   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/front/iiscars.php");
} else {
   //per default, display object
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : 0);
   $iiscars->display(
      [
         'id'           => $_GET['id'],
         'withtemplate' => $withtemplate
      ]
   );
}
   


Html::footer();