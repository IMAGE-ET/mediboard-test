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
$userfunction->load(CValue::getOrSession("function_id", 0));
if($userfunction->_id) {
  $userfunction->loadRefsFwd();
  $userfunction->loadBackRefs("users");
  foreach($userfunction->_back["users"] as &$curr_user) {
    $curr_user->loadRefProfile();
  }
  $userfunction->loadBackRefs("secondary_functions");
  foreach($userfunction->_back["secondary_functions"] as &$curr_sec_function) {
    $curr_sec_function->loadRefUser();
    $curr_sec_function->_ref_user->loadRefProfile();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("userfunction"        , $userfunction);
$smarty->assign("listGroups"          , $listGroups  );
$smarty->assign("secondary_function"  , new CSecondaryFunction());
$smarty->assign("utypes"              , CUser::$types );

$smarty->display("vw_idx_functions.tpl");

?>