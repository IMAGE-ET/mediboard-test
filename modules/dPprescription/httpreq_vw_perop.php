<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");
$operation_id = CValue::getOrSession("operation_id");

$operation = new COperation();
$operation->load($operation_id);

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPrescriptionSejour();
$prescription_id = $sejour->_ref_prescription_sejour->_id;

$lines = array();
$filter_lines = array();

if($prescription_id){
  $prescription = $sejour->_ref_prescription_sejour;
	$prescription->calculAllPlanifSysteme(true);

	// Chargement des lignes de medicaments perop
	$prescription_line_medicament = new CPrescriptionLineMedicament();
	$prescription_line_medicament->prescription_id = $prescription_id;
	$prescription_line_medicament->perop = 1;
	$lines_med = $prescription_line_medicament->loadMatchingList();
	
	// Chargement des lignes d'elements perop
	$prescription_line_element = new CPrescriptionLineElement();
	$prescription_line_element->prescription_id = $prescription_id;
	$prescription_line_element->perop = 1;
	$lines_elt = $prescription_line_element->loadMatchingList();
	
	// Chargement des lignes mix perop
	$prescription_line_mix = new CPrescriptionLineMix();
	$prescription_line_mix->prescription_id = $prescription_id;
	$prescription_line_mix->perop = 1;
	$lines_mix = $prescription_line_mix->loadMatchingList();
	
	foreach($lines_med as $_line_med){
		$_line_med->loadBackRefs("planifications", "dateTime");
		
		foreach($_line_med->_back["planifications"] as $_planif){
      $_planif->loadTargetObject();
      $_planif->_ref_object->loadRefsFwd();
      $_planif->loadRefPrise();
			$lines[$_line_med->_guid]["planifications"][$_planif->dateTime.$_line_med->_id][$_planif->_id] = $_planif;
		}
		$_line_med->loadRefsAdministrations();
		foreach($_line_med->_ref_administrations as $_adm){
			$_adm->loadTargetObject();
			$_adm->_ref_object->loadRefsFwd();
		}

		$_line_med->_chapitre = "med";
		$lines[$_line_med->_guid]["administrations"][$_line_med->_id] = $_line_med->_ref_administrations;
		$_line_med->_count_adm = count($_line_med->_ref_administrations);
		$lines[$_line_med->_guid]["object"] = $_line_med;
  }
	
	foreach($lines_elt as $_elt){
		$_elt->loadBackRefs("planifications", "dateTime");

		foreach($_elt->_back["planifications"] as $_planif){
      $_planif->loadRefPrise();
      $lines[$_elt->_guid]["planifications"][$_planif->dateTime.$_elt->_id][$_planif->_id] = $_planif; 
		}
		
		$_elt->loadRefsAdministrations();
		$lines[$_elt->_guid]["administrations"][$_elt->_id] = $_elt->_ref_administrations;
	  $_elt->_count_adm = count($_elt->_ref_administrations);
		$lines[$_elt->_guid]["object"] = $_elt;
	}
	
	foreach($lines_mix as $_mix){
		$_mix->loadRefsLines();
		$_mix->_chapitre = "perfusion";

		foreach($_mix->_ref_lines as $_mix_item){
			$_mix_item->updateQuantiteAdministration();
			$_mix_item->loadbackRefs("planifications", "dateTime");

			foreach($_mix_item->_back["planifications"] as $_planif){
        $_planif->loadTargetObject();
        $_planif->_ref_object->loadRefsFwd();
        $_planif->loadRefPrise();
        $lines[$_mix->_guid]["planifications"][$_planif->dateTime.$_mix_item->_id][$_planif->_id] = $_planif; 
      }
			
			$_mix_item->loadRefsAdministrations();
			foreach($_mix_item->_ref_administrations as $_adm){
        $_adm->loadTargetObject();
        $_adm->_ref_object->loadRefsFwd();
      }
			
      $lines[$_mix->_guid]["administrations"][$_mix_item->_id] = $_mix_item->_ref_administrations;
			$_mix->_count_adm += count($_mix_item->_ref_administrations);

			$lines[$_mix->_guid]["object"] = $_mix;
		}
	}
}

function sortLines($line1, $line2){
  if(isset($line1["planifications"]) && isset($line2["planifications"])){
		reset($line1["planifications"]);
		reset($line2["planifications"]);
		return key($line1["planifications"]) < key($line2["planifications"]) ? -1 : 1;
  }
}

usort($lines, "sortLines");

// Chargement des anesths
$anesth = new CMediusers();
$anesths = $anesth->loadAnesthesistes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("anesths", $anesths);
$smarty->assign("operation", $operation);
$smarty->display("inc_vw_perop.tpl");

?>