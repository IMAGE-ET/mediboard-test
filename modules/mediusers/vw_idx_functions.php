<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Récupération des fonctions
$listGroups = new CGroups;
$order = "text";
$listGroups = $listGroups->loadListWithPerms(PERM_EDIT, null, $order);

foreach($listGroups as $key => $value) {
  $listGroups[$key]->loadRefs();
  foreach($listGroups[$key]->_ref_functions as $key2 => $value2) {
    $listGroups[$key]->_ref_functions[$key2]->loadRefs();
  }
}

// Récupération de la fonction selectionnée
$userfunction = new CFunctions;
$userfunction->load(mbGetValueFromGetOrSession("function_id", 0));
$userfunction->loadRefsFwd();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("userfunction", $userfunction);
$smarty->assign("listGroups"  , $listGroups  );

$smarty->display("vw_idx_functions.tpl");

?>