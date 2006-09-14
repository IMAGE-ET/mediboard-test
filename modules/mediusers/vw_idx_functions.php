<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Récupération des fonctions
$listGroups = new CGroups;
$listGroups = $listGroups->loadList();

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
$smarty = new CSmartyDP(1);

$smarty->assign("userfunction", $userfunction);
$smarty->assign("listGroups"  , $listGroups  );

$smarty->display("vw_idx_functions.tpl");

?>