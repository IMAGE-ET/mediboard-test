<?php /* $Id: ajax_vw_prestations.php $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");
$affectation_id = CValue::get("affectation_id");
$prestations_j = CPrestationJournaliere::loadCurrentList();

foreach ($prestations_j as $_prestation) {
  $_prestation->_ref_items = $_prestation->loadBackRefs("items", "rank");
}

if ($affectation_id) {
  $affectation = new CAffectation;
  $affectation->load($affectation_id);
  $sejour_id = $affectation->sejour_id;
}

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefsAffectations("sortie ASC");

$duree = strtotime($sejour->sortie) - strtotime($sejour->entree);
$affectations = $sejour->_ref_affectations;

$dates = array();

$tableau_prestations_j = array();
$tableau_prestations_p = array();
$prestations_p         = array();

if (count($affectations)) {
  foreach ($affectations as $_affectation) {
    $dif = strtotime($_affectation->sortie) - strtotime($_affectation->entree);
    $_affectation->_width = ($dif / $duree)*100;
    $date_temp = mbDate($_affectation->entree);
    
    while ($date_temp <= mbDate($_affectation->sortie)) {
      $dates[$date_temp] = $_affectation->_id;
      $date_temp = mbDate("+1 day", $date_temp);
    }
    
    $items_liaisons = $_affectation->loadBackRefs("items_liaisons");
    CMbObject::massLoadFwdRef($items_liaisons, "item_prestation_id");
    
    foreach ($items_liaisons as $_item_liaison) {
      $_item = $_item_liaison->loadRefItem();
      
      switch($_item->object_class) {
        case "CPrestationJournaliere":
        	@$tableau_prestations_j[$_item_liaison->date][$_item->object_id]["souhait"] = $_item;
          $_item_realise = $_item_liaison->loadRefItemRealise();
          if ($_item_realise->_id) {
            @$tableau_prestations_j[$_item_liaison->date][$_item->object_id]["realise"] = $_item_realise;
          }
        	break;
        case "CPrestationPonctuelle":
        	if (!isset($prestations_p[$_item->object_id])) {
        	  $prestation = new CPrestationPonctuelle;
            $prestation->load($_item->object_id);
            $prestation->_ref_items = $prestation->loadBackRefs("items");
            $prestations_p[$_item->object_id] = $prestation;
        	}
          @$tableau_prestations_p[$_item_liaison->date][$_item->object_id][$_item->_id] = $_item_liaison->quantite;
      }
    }
  }  
}

$smarty = new CSmartyDP;

$smarty->assign("dates"      , $dates);
$smarty->assign("sejour"     , $sejour);
$smarty->assign("affectations", $affectations);
$smarty->assign("prestations_j", $prestations_j);
$smarty->assign("prestations_p", $prestations_p);
$smarty->assign("tableau_prestations_p", $tableau_prestations_p);
$smarty->assign("tableau_prestations_j", $tableau_prestations_j);

$smarty->display("inc_vw_prestations.tpl");
?>