<?php /* $Id: ajax_vw_prestations.php $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id      = CValue::get("sejour_id");
$vue_prestation = CValue::get("vue_prestation", "all");

$prestations_j = CPrestationJournaliere::loadCurrentList();
$dates         = array();
$prestations_p = array();
$liaisons_j    = array();
$liaisons_p    = array();

foreach ($prestations_j as $_prestation) {
  $_prestation->_ref_items = $_prestation->loadBackRefs("items", "rank");
}

$sejour = new CSejour;
$sejour->load($sejour_id);

$duree = strtotime($sejour->sortie) - strtotime($sejour->entree);
$affectations = $sejour->loadRefsAffectations();

$date_temp = mbDate($sejour->entree);

while ($date_temp <= mbDate($sejour->sortie)) {
  if (!isset($dates[$date_temp])) {
    $dates[$date_temp] = 0;
  }
  $date_temp = mbDate("+1 day", $date_temp);
}

if (count($affectations)) {
  $lits = CMbObject::massLoadFwdRef($affectations, "lit_id");
  CMbObject::massLoadFwdRef($lits, "chambre_id");
  foreach ($affectations as $_affectation) {
    $_affectation->loadRefLit()->loadCompleteView();
    $_affectation->_rowspan = mbDaysRelative($_affectation->entree, $_affectation->sortie)+1;
    $date_temp = mbDate($_affectation->entree);
  
    while ($date_temp <= mbDate($_affectation->sortie)) {
      $dates[$date_temp] = $_affectation->_id;
      $date_temp = mbDate("+1 day", $date_temp);
    }
  }
}

$items_liaisons = $sejour->loadBackRefs("items_liaisons");
CMbObject::massLoadFwdRef($items_liaisons, "item_prestation_id");
CMbObject::massLoadFwdRef($items_liaisons, "item_prestation_realise_id");

foreach ($items_liaisons as $_item_liaison) {
  $_item = $_item_liaison->loadRefItem();
  
  if (!$_item->_id) {
    $_item = $_item_liaison->loadRefItemRealise();
  }
  
  switch($_item->object_class) {
    case "CPrestationJournaliere":
      $liaisons_j[$_item_liaison->date][$_item->object_id] = $_item_liaison;
      break;
    case "CPrestationPonctuelle":
      $liaisons_p[$_item_liaison->date][$_item->object_id][] = $_item_liaison;
      if (!isset($prestations_p[$_item->object_id])) {
        $prestation = new CPrestationPonctuelle;
        $prestation->load($_item->object_id);
        $prestation->_ref_items = $prestation->loadBackRefs("items");
        $prestations_p[$_item->object_id] = $prestation;
      }
  }
}

$type_j = array("souhait");

if ($vue_prestation == "all") {
  $type_j[] = "realise";
}

$smarty = new CSmartyDP;

$smarty->assign("today"      , mbDate());
$smarty->assign("dates"      , $dates);
$smarty->assign("sejour"     , $sejour);
$smarty->assign("affectations", $affectations);
$smarty->assign("prestations_j", $prestations_j);
$smarty->assign("prestations_p", $prestations_p);
$smarty->assign("empty_liaison", new CItemLiaison);
$smarty->assign("liaisons_p", $liaisons_p);
$smarty->assign("liaisons_j", $liaisons_j);
$smarty->assign("type_j"    , $type_j);
$smarty->assign("vue_prestation", $vue_prestation);

$smarty->display("inc_vw_prestations.tpl");
?>