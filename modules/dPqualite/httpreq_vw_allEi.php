<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $AppUI;
$can->needsRead();

$type              = CValue::get("type");
$first             = CValue::get("first");
$selected_user_id  = CValue::get("selected_user_id");
$selected_service_valid_user_id = CValue::get("selected_service_valid_user_id");
$elem_concerne     = CValue::get("elem_concerne");
$evenements        = CValue::get("evenements");

CValue::setSession("selected_user_id", $selected_user_id);
CValue::setSession("selected_service_valid_user_id", $selected_service_valid_user_id);
CValue::setSession("elem_concerne", $elem_concerne);
CValue::setSession("evenements", $evenements);

$selected_fiche_id = CValue::getOrSession("selected_fiche_id");

$where = array();
if ($elem_concerne) {
  $where["fiches_ei.elem_concerne"] = "= '$elem_concerne'";
}

if($selected_user_id){
  $where["fiches_ei.user_id"] = "= '$selected_user_id'";
}

if($selected_service_valid_user_id){
  $where["fiches_ei.service_valid_user_id"] = "= '$selected_service_valid_user_id'";
}

$user_id = null;
if($type == "AUTHOR" || ($can->edit && !$can->admin)){
  $user_id = $AppUI->user_id;
}

if ($evenements) {
  $listeFiches = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, false);
  $item = new CEiItem;
  $item->ei_categorie_id = $evenements;
  $listTypes = array_keys($item->loadMatchingList());

  foreach($listeFiches as $id => $fiche) {
    if (count(array_intersect($fiche->_ref_evenement, $listTypes)) == 0) {
      unset($listeFiches[$id]);
    }
  }
  $countFiches = count($listeFiches);
}

else {
  $countFiches = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, true);
  $listeFiches = CFicheEi::loadFichesEtat($type, $user_id, $where, 0, false, $countFiches > 20 ? $first : null);
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listeFiches"      , $listeFiches);
$smarty->assign("countFiches"      , $countFiches);
$smarty->assign("type"             , $type);
$smarty->assign("first"            , $first);
$smarty->assign("selected_fiche_id", $selected_fiche_id);

$smarty->display("inc_ei_liste.tpl");

?>