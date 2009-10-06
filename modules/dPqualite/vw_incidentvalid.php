<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;
$can->needsRead();

$fiche_ei_id         = mbGetValueFromGetOrSession("fiche_ei_id",null);
$ficheAnnuleVisible  = mbGetValueFromGetOrSession("ficheAnnuleVisible" , 0);
$ficheTermineVisible = mbGetValueFromGetOrSession("ficheTermineVisible" , 0);

$selected_user_id    = mbGetValueFromGetOrSession("selected_user_id");
$selected_service_valid_user_id = mbGetValueFromGetOrSession("selected_service_valid_user_id");
$elem_concerne     = mbGetValueFromGetOrSession("elem_concerne");
$evenements        = mbGetValueFromGetOrSession("evenements");

$catFiche = array();
$fiche = new CFicheEi();

$droitFiche = !$fiche->load($fiche_ei_id);
$droitFiche = $droitFiche || (!$can->edit && $fiche->user_id != $AppUI->user_id);
$droitFiche = $droitFiche || ($can->edit && !$can->admin && $fiche->user_id != $AppUI->user_id && $fiche->service_valid_user_id != $AppUI->user_id);

// Liste des Catégories d'EI
$listCategories = new CEiCategorie;
$listCategories = $listCategories->loadList(null, "nom");

if($droitFiche){
  // Cette fiche n'est pas valide
  $fiche_ei_id = null;
  mbSetValueToSession("fiche_ei_id");
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

$smarty->assign("today"            , mbDate());
$smarty->assign("listUsersEdit"    , $listUsersEdit);
$smarty->assign("listUsersTermine" , $listUsersTermine);
$smarty->assign("selectedUser"     , $selectedUser);
$smarty->assign("selected_fiche_id", $fiche_ei_id);
$smarty->assign("listCategories"   , $listCategories);

$smarty->display("vw_incidentvalid.tpl");
?>