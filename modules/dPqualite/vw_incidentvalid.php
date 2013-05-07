<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$user = CUser::get();

$fiche_ei_id         = CValue::getOrSession("fiche_ei_id",null);
$ficheAnnuleVisible  = CValue::getOrSession("ficheAnnuleVisible" , 0);
$ficheTermineVisible = CValue::getOrSession("ficheTermineVisible" , 0);

$selected_user_id    = CValue::getOrSession("selected_user_id");
$selected_service_valid_user_id = CValue::getOrSession("selected_service_valid_user_id");
$elem_concerne     = CValue::getOrSession("elem_concerne");
$evenements        = CValue::getOrSession("evenements");
$filter_item       = CValue::getOrSession("filter_item");

$catFiche = array();
$fiche = new CFicheEi();

$droitFiche = !$fiche->load($fiche_ei_id);
$droitFiche = $droitFiche || (!CCanDo::edit() && $fiche->user_id != $user->_id);
$droitFiche = $droitFiche || (CCanDo::edit() && !CCanDo::admin() && $fiche->user_id != $user->_id && $fiche->service_valid_user_id != $user->_id);

// Liste des Catégories d'EI
$listCategories = new CEiCategorie;
$listCategories = $listCategories->loadList(null, "nom");

if($droitFiche){
  // Cette fiche n'est pas valide
  $fiche_ei_id = null;
  CValue::setSession("fiche_ei_id");
  $fiche = new CFicheEi;
}
else {
  $fiche->loadRefsFwd();
  $fiche->loadRefItems();
  
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

$listUsersTermine = new CMediusers;
$listUsersTermine = $listUsersTermine->loadListFromType();

// Chargement de la liste des Chef de services / utilisateur
$module = CModule::getInstalled("dPqualite");
$perm = new CPermModule;

$listUsersEdit = new CMediusers;
$listUsersEdit = $listUsersEdit->loadListFromType(null, PERM_READ);
foreach($listUsersEdit as $keyUser => $infoUser){
  if(!$perm->getInfoModule("permission", $module->mod_id, PERM_EDIT, $keyUser)){
    unset($listUsersEdit[$keyUser]);
  }
}

$items = array();

if ($evenements) {
	$where = array();
	$where["ei_categorie_id"] = " = '$evenements'";
	$item = new CEiItem;
	$items = $item->loadList($where);
}

$selectedUser = new CMediusers();
$selectedUser->load($selected_user_id);

$filterFiche = new CFicheEi;
$filterFiche->elem_concerne = $elem_concerne;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("catFiche"         , $catFiche);
$smarty->assign("fiche"            , $fiche);

$smarty->assign("filterFiche"      , $filterFiche);
$smarty->assign("selected_user_id", $selected_user_id);
$smarty->assign("selected_service_valid_user_id", $selected_service_valid_user_id);
$smarty->assign("evenements", $evenements);

$smarty->assign("today"            , CMbDT::date());
$smarty->assign("listUsersEdit"    , $listUsersEdit);
$smarty->assign("listUsersTermine" , $listUsersTermine);
$smarty->assign("selectedUser"     , $selectedUser);
$smarty->assign("selected_fiche_id", $fiche_ei_id);
$smarty->assign("listCategories"   , $listCategories);
$smarty->assign("items"            , $items);
$smarty->assign("filter_item"      , $filter_item);

$smarty->display("vw_incidentvalid.tpl");
