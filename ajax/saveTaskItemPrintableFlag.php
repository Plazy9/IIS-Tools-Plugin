<?php
use Html;
use Session;
use PluginIistoolsTicketprint;

$AJAX_INCLUDE = 1;
include("../../../inc/includes.php");
header("Content-Type: text/html; charset=UTF-8");

Html::header_nocache();
Session::checkLoginUser();


$ret = 0;
if ( isset($_POST["ticketItem_id"]) && isset($_POST["ticketItem_class"])) {
   // then we may have something to unlock
    $pi = new PluginIistoolsTicketprint();

    if ($_POST["value"]) {
        $ret="add ";
      $ret.=$pi->addItemToPrint($_POST["ticketItem_id"],$_POST["ticketItem_class"] );
    } else {
        $ret="remove ";
       echo  $pi->removeItemToPrint($_POST["ticketItem_id"], $_POST["ticketItem_class"]);
    }
    $ret.="Success ".$_POST["ticketItem_class"]." - ".$_POST["ticketItem_id"]. " - ".$_POST['value'];
} else {
    $ret="Failed";
}

echo $ret;
