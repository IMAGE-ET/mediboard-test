<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Liste des praticiens accessibles
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

// Liste des comptes-rendus persos
$user_id = mbGetValueFromGetOrSession("filter_user_id", $AppUI->user_id);
if(!$user_id){
	mbSetValueToSession("filter_user_id", $AppUI->user_id);
  $user_id = $AppUI->user_id;
}
$user = new CMediusers;
$user->load($user_id);
if(!$user->isPraticien()) {
  $user_id = 0;
  $user->load($user_id);
}


if($user_id){
  $where = array();
  $where["chir_id"] = "= '$user_id'";
  $order = "chir_id ASC, nom ASC";
  $listesPrat = new CListeChoix();
  $listesPrat = $listesPrat->loadList($where, $order);
  foreach($listesPrat as $key => $value) {
    $listesPrat[$key]->loadRefsFwd();
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
  
  // Liste des compte-rendus selectionnables
    // Praticien
  $where = array();
  $where["chir_id"] = "= '$user_id'";
  $order = "object_class, nom";
  $listCrPrat = new CCompteRendu;
  $listCrPrat = $listCrPrat->loadList($where, $order);
    // Cabinet
  $where = array();
  $where["function_id"] = "= '$user->function_id'";
  $order = "object_class, nom";
  $listCrFunc = new CCompteRendu;
  $listCrFunc = $listCrFunc->loadList($where, $order);
}else{
  $listCrPrat = array();
  $listCrFunc = array();
  $listesFunc = array();
  $listesPrat = array();
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
$smarty = new CSmartyDP(1);

$smarty->assign("users"     , $listPrat  );
$smarty->assign("user_id"   , $user_id   );
$smarty->assign("listPrat"  , $listPrat  );
$smarty->assign("listFunc"  , $listFunc  );
$smarty->assign("listCrPrat", $listCrPrat);
$smarty->assign("listCrFunc", $listCrFunc);
$smarty->assign("listesPrat", $listesPrat);
$smarty->assign("listesFunc", $listesFunc);
$smarty->assign("liste"     , $liste     );

$smarty->display("vw_idx_listes.tpl");

?>