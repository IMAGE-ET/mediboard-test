<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();

// Liste des utilisateurs accessibles
$user = new CMediusers();
$listUser = $user->loadUsers(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

$listEtab = array(CGroups::loadCurrent());

// Liste des comptes-rendus persos
$user_id = mbGetValueFromGetOrSession("filter_user_id", $AppUI->user_id);
if(!$user_id){
	mbSetValueToSession("filter_user_id", $AppUI->user_id);
  $user_id = $AppUI->user_id;
}

$user->load($user_id);
$user->loadRefFunction();

$listCrUser = array();
$listCrFunc = array();
$listCrEtab = array();

$listesFunc = array();
$listesUser = array();
$listesEtab = array();

if($user_id){
  $where = array();
  $where["chir_id"] = "= '$user_id'";
  $order = "chir_id ASC, nom ASC";
  
  $listesUser = new CListeChoix();
  $listesUser = $listesUser->loadList($where, $order);
  foreach($listesUser as $key => $value) {
    $listesUser[$key]->loadRefsFwd();
  }
  
  // Liste des comptes-rendus de cabinet
  $where = array();
  $where["function_id"] = "= '$user->function_id'"; 
  $order = "function_id ASC, nom ASC";
  
  $listesFunc = new CListeChoix();
  $listesFunc = $listesFunc->loadList($where, $order);
  foreach($listesFunc as $key => $value) {
    $listesFunc[$key]->loadRefsFwd();
  }
  
  // Liste des comptes-rendus d'etablissement
  $where = array();
  $where["group_id"] = "= '{$user->_ref_function->group_id}'"; 
  $order = "group_id ASC, nom ASC";
  
  $listesEtab = new CListeChoix();
  $listesEtab = $listesEtab->loadList($where, $order);
  foreach($listesEtab as $key => $value) {
    $listesEtab[$key]->loadRefsFwd();
  }
  
  // Liste des compte-rendus selectionnables
    // Praticien
  $where = array();
  $where["chir_id"] = "= '$user_id'";
  $order = "object_class, nom";
  $listCrUser = new CCompteRendu;
  $listCrUser = $listCrUser->loadList($where, $order);
  
    // Cabinet
  $where = array();
  $where["function_id"] = "= '$user->function_id'";
  $order = "object_class, nom";
  $listCrFunc = new CCompteRendu;
  $listCrFunc = $listCrFunc->loadList($where, $order);
  
    // Etablissement
  $where = array();
  $where["group_id"] = "= '{$user->_ref_function->group_id}'";
  $order = "object_class, nom";
  $listCrEtab = new CCompteRendu;
  $listCrEtab = $listCrEtab->loadList($where, $order);
}

// liste sélectionnée
$liste_id = mbGetValueFromGetOrSession("liste_id");
$liste = new CListeChoix();
$liste->load($liste_id); 
$liste->loadRefsFwd();

//if (!$liste_id) {
//  $liste->chir_id = $AppUI->user_id;
//}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("users"     , $listUser  );
$smarty->assign("user_id"   , $user_id   );

$smarty->assign("listPrat"  , $listUser  );
$smarty->assign("listFunc"  , $listFunc  );
$smarty->assign("listEtab"  , $listEtab  );

$smarty->assign("listCrUser", $listCrUser);
$smarty->assign("listCrFunc", $listCrFunc);
$smarty->assign("listCrEtab", $listCrEtab);

$smarty->assign("listesUser", $listesUser);
$smarty->assign("listesFunc", $listesFunc);
$smarty->assign("listesEtab", $listesEtab);

$smarty->assign("liste"     , $liste);

$smarty->display("vw_idx_listes.tpl");

?>