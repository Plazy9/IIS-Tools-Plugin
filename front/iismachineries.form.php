<?php
use Glpi\Event;
use GlpiPlugin\Iistools\iisMachineries;

include ('../../../inc/includes.php');

$iismachineries = new iisMachineries();

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('iistools') || !$plugin->isActivated('iistools')) {
  Html::displayNotFoundError();
}


Html::header(iisMachineries::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "plugins", iisMachineries::class, '');


if (isset($_POST['add'])) {
   
   //Check CREATE ACL
   $iismachineries->check(-1, CREATE, $_POST);
   //Do object creation

   if ($newid = $iismachineries->add($_POST)){
      Event::log(
         $newid,
         "iisMachineries",
         4,
         "inventory",
         sprintf(__('%1$s adds the item %2$s'), $_SESSION["glpiname"], $_POST["name"])
     );
   }
   //Redirect to newly created object form
   //Html::redirect("{$CFG_GLPI['root_doc']}/plugins/iistools/front/iismachineries.form.php?id=$newid");
   Html::back();
} else if (isset($_POST['update'])) {
   //Check UPDATE ACL
   $iismachineries->check($_POST['id'], UPDATE);
   //Do object update
   $iismachineries->update($_POST);
   //Redirect to object form
   Html::back();
} else if (isset($_POST['delete'])) {
   //Check DELETE ACL
   $iismachineries->check($_POST['id'], DELETE);
   //Put object in dustbin
   $iismachineries->delete($_POST);
   //Redirect to objects list
   $iismachineries->redirectToList();
} else if (isset($_POST['purge'])) {
   //Check PURGE ACL
   $iismachineries->check($_POST['id'], PURGE);
   //Do object purge
   $iismachineries->delete($_POST, 1);
   //Redirect to objects list
   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/iistools/front/iismachineries.php");
} else {
   //per default, display object
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : 0);
   $iismachineries->display(
      [
         'id'           => $_GET['id'],
         'withtemplate' => $withtemplate
      ]
   );
}
   


Html::footer();