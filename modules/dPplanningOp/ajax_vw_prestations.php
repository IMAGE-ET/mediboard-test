<?php /* $Id: ajax_vw_prestations.php $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");
$context   = CValue::get("context", "all");

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
CMbObject::massLoadFwdRef($items_liaisons, "item_souhait_id");
CMbObject::massLoadFwdRef($items_liaisons, "item_realise_id");

foreach ($items_liaisons as $_item_liaison) {
  $_item = $_item_liaison->loadRefItem();
  
  $_item_liaison->loadRefItemRealise();
  if (!$_item->_id) {
    $_item = $_item_liaison->_ref_item_realise;
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

$date_temp = mbDate($sejour->entree);

while (!isset($liaisons_j[$date_temp]) && $date_temp < mbDate($sejour->sortie)) {
  $date_temp = mbDate("+1 day", $date_temp);
}

$liaisons_j_date =& $liaisons_j[$date_temp];
$save_state = array();

foreach ($prestations_j as $_prestation_id => $_prestation) {
  $item_liaison = new CItemLiaison;
  $item_liaison->_id = "temp";
  $item_liaison->loadRefItem();
  $item_liaison->loadRefItemRealise();
  
  if (isset($liaisons_j_date[$_prestation_id])) {
    $save_liaison = $liaisons_j_date[$_prestation_id];
    
      $item_liaison->item_souhait_id         = $save_liaison->item_souhait_id;
      $item_liaison->item_realise_id         = $save_liaison->item_realise_id;
      $item_liaison->_ref_item->_id          = $save_liaison->_ref_item->_id;
      $item_liaison->_ref_item->nom          = $save_liaison->_ref_item->nom;
      $item_liaison->_ref_item->rank         = $save_liaison->_ref_item->rank;
      $item_liaison->_ref_item_realise->_id  = $save_liaison->_ref_item_realise->_id;
      $item_liaison->_ref_item_realise->nom  = $save_liaison->_ref_item_realise->nom;
      $item_liaison->_ref_item_realise->rank = $save_liaison->_ref_item_realise->rank;
    
    $save_state[$_prestation_id] = $item_liaison;
  }
  else {
    $save_state[$_prestation_id] = $item_liaison;
    $liaisons_j_date[$_prestation_id] = $item_liaison;
  }
}

foreach ($dates as $_date => $_value) {
  if ($_date <= $date_temp) {
    continue;
  }
  if (!isset($liaisons_j[$_date])) {
    //mbTrace($_date);
    $liaisons_j[$_date] = array();
  }
  $liaisons_j_date =& $liaisons_j[$_date];
  
  foreach ($prestations_j as $_prestation_id => $_prestation) {
    $item_liaison = new CItemLiaison;
    $item_liaison->_id = "temp";
    $item_liaison->loadRefItem();
    $item_liaison->loadRefItemRealise();
    
    if (isset($liaisons_j_date[$_prestation_id])) {
      $save_liaison = $liaisons_j_date[$_prestation_id];
      
        $item_liaison->item_souhait_id         = $save_liaison->item_souhait_id;
        $item_liaison->item_realise_id         = $save_liaison->item_realise_id;
        $item_liaison->_ref_item->_id          = $save_liaison->_ref_item->_id;
        $item_liaison->_ref_item->nom          = $save_liaison->_ref_item->nom;
        $item_liaison->_ref_item->rank         = $save_liaison->_ref_item->rank;
        $item_liaison->_ref_item_realise->_id  = $save_liaison->_ref_item_realise->_id;
        $item_liaison->_ref_item_realise->nom  = $save_liaison->_ref_item_realise->nom;
        $item_liaison->_ref_item_realise->rank = $save_liaison->_ref_item_realise->rank;
      
      $save_state[$_prestation_id] = $item_liaison;
    }
    else {
      $save_liaison = $save_state[$_prestation_id];
      
        $item_liaison->item_souhait_id         = $save_liaison->item_souhait_id;
        $item_liaison->item_realise_id         = $save_liaison->item_realise_id;
        $item_liaison->_ref_item->_id          = $save_liaison->_ref_item->_id;
        $item_liaison->_ref_item->nom          = $save_liaison->_ref_item->nom;
        $item_liaison->_ref_item->rank         = $save_liaison->_ref_item->rank;
        $item_liaison->_ref_item_realise->_id  = $save_liaison->_ref_item_realise->_id;
        $item_liaison->_ref_item_realise->nom  = $save_liaison->_ref_item_realise->nom;
        $item_liaison->_ref_item_realise->rank = $save_liaison->_ref_item_realise->rank;
      
      $liaisons_j_date[$_prestation_id] = $item_liaison;
      
    }
  }
}

$empty_liaison = new CItemLiaison;
$empty_liaison->_id = "temp";
$empty_liaison->loadRefItem();
$empty_liaison->loadRefItemRealise();
$smarty = new CSmartyDP;

$smarty->assign("today"        , mbDate());
$smarty->assign("dates"        , $dates);
$smarty->assign("sejour"       , $sejour);
$smarty->assign("affectations" , $affectations);
$smarty->assign("prestations_j", $prestations_j);
$smarty->assign("prestations_p", $prestations_p);
$smarty->assign("empty_liaison", $empty_liaison);
$smarty->assign("liaisons_p"   , $liaisons_p);
$smarty->assign("liaisons_j"   , $liaisons_j);
$smarty->assign("context"      , $context);
$smarty->assign("bank_holidays", mbBankHolidays(mbDate()));
$smarty->display("inc_vw_prestations.tpl");
?>