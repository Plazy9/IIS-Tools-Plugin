<?php
use GlpiPlugin\Iistools\iisCameras;

include ('../../../inc/includes.php');

$iiscameras = new iisCameras();

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('iistools') || !$plugin->isActivated('iistools')) {
  Html::displayNotFoundError();
}


Html::header(iisCameras::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisCameras::class, '');


if (isset($_POST['add'])) {
   
   //Check CREATE ACL
   $iiscameras->check(-1, CREATE, $_POST);
   //Do object creation
   $newid = $iiscameras->add($_POST);
   //Redirect to newly created object form
   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/iistools/front/iiscameras.form.php?id=$newid");
} else if (isset($_POST['update'])) {
   //Check UPDATE ACL
   $iiscameras->check($_POST['id'], UPDATE);
   //Do object update
   $iiscameras->update($_POST);
   //Redirect to object form
   Html::back();
} else if (isset($_POST['delete'])) {
   //Check DELETE ACL
   $iiscameras->check($_POST['id'], DELETE);
   //Put object in dustbin
   $iiscameras->delete($_POST);
   //Redirect to objects list
   $iiscameras->redirectToList();
} else if (isset($_POST['purge'])) {
   //Check PURGE ACL
   $iiscameras->check($_POST['id'], PURGE);
   //Do object purge
   $iiscameras->delete($_POST, 1);
   //Redirect to objects list
   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/iistools/front/iiscameras.php");
} else {
   //per default, display object
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : 0);
   $iiscameras->display(
      [
         'id'           => $_GET['id'],
         'withtemplate' => $withtemplate
      ]
   );
}
   


Html::footer();