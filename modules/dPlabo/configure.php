<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Thomas Despoix
*/

global $can, $m, $AppUI, $dPconfig;

$can->needsAdmin();


$config = $dPconfig[$m]["CCatalogueLabo"];
$remote_name = $config["remote_name"];

$id400 = new CIdSante400();
$id400->object_class = "CMediusers";
$id400->tag = $remote_name;
$order = "last_update DESC";

$list_idSante400 = $id400->loadMatchingList($order);

foreach ($list_idSante400 as $curr_idSante400) {
  $curr_idSante400->loadRefs();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_idSante400",$list_idSante400);

$smarty->display("configure.tpl");

?>