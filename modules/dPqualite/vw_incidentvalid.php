<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can;
$can->needsRead();

$fiche_ei_id         = mbGetValueFromGetOrSession("fiche_ei_id",null);
$ficheAnnuleVisible  = mbGetValueFromGetOrSession("ficheAnnuleVisible" , 0);
$ficheTermineVisible = mbGetValueFromGetOrSession("ficheTermineVisible" , 0);
$selected_user_id    = mbGetValueFromGetOrSession("selected_user_id");
$first               = mbGetValueFromGetOrSession("first");

$catFiche = array();
$fiche = new CFicheEi();

$droitFiche = !$fiche->load($fiche_ei_id);
$droitFiche = $droitFiche || (!$can->edit && $fiche->user_id != $AppUI->user_id);
$droitFiche = $droitFiche || ($can->edit && !$can->admin && $fiche->user_id != $AppUI->user_id && $fiche->service_valid_user_id != $AppUI->user_id);

if($droitFiche){
  // Cette fiche n'est pas valide
  $fiche_ei_id = null;
  mbSetValueToSession("fiche_ei_id");
  $fiche = new CFicheEi;
}
else {
  $fiche->loadRefsFwd();
  $fiche->loadRefItems();
  
  // Liste des Catégories d'EI
  $listCategories = new CEiCategorie;
  $listCategories = $listCategories->loadList(null, "nom");

  foreach($listCategories as $keyCat => $valueCat){
    foreach($fiche->_ref_items as $keyItem => $valueItem){
      if($fiche->_ref_items[$keyItem]->ei_categorie_id == $keyCat){
        if(!isset($catFiche[$listCategories[$keyCat]->nom])){
          $catFiche[$listCategories[$keyCat]->nom] = array();
        }
        $catFiche[$listCategories[$keyCat]->nom][] = $fiche->_ref_items[$keyItem];
      }
    }
  }
}

// Chargement pour Administration
$listUsersEdit = new CMediusers;
if($can->admin){
  // Chargement de la liste des Chef de services / utilisateur
  $module = CModule::getInstalled("dPqualite");
  $permUserEdit = new CPermModule;
  
  if (!$fiche->date_validation && !$fiche->annulee) {
	  $listUsersEdit = $listUsersEdit->loadListFromType(null, PERM_READ);
	  foreach($listUsersEdit as $keyUser => $infoUser){
	    if(!$permUserEdit->getInfoModule("permission", $module->mod_id, PERM_EDIT, $keyUser)){
	      unset($listUsersEdit[$keyUser]);
	    }
	  }
  }
}

$selectedUser = new CMediusers();
$selectedUser->load($selected_user_id);

$listCounts = array();
$listCounts["AUTHOR"]      = 0;
$listCounts["VALID_FICHE"] = 0;
$listCounts["ATT_CS"]      = 0;
$listCounts["ATT_QUALITE"] = 0;
$listCounts["ATT_VERIF"]   = 0;
$listCounts["ATT_CTRL"]    = 0;
$listCounts["ALL_TERM"]    = 0;
$listCounts["ANNULE"]      = 0;

foreach ($listCounts as $type => &$count) {
  $user_id = null;
  $where = null;
  if($type == "AUTHOR" || ($can->edit && !$can->admin)){
    $user_id = $AppUI->user_id;
  }
  
  if($type == "ALL_TERM" && $can->admin){
    $where = array();
    if($selected_user_id){
      $where["fiches_ei.user_id"] = "= '$selected_user_id'";
    }
  }
  $count = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, true);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("catFiche"         , $catFiche);
$smarty->assign("fiche"            , $fiche);
$smarty->assign("first"            , $first);
$smarty->assign("today"            , mbDate());
$smarty->assign("listUsersEdit"    , $listUsersEdit);
$smarty->assign("listCounts"       , $listCounts);
$smarty->assign("selectedUser"     , $selectedUser);
$smarty->assign("selected_fiche_id", $fiche_ei_id);

$smarty->display("vw_incidentvalid.tpl");
?>