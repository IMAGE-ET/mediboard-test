<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPrescriptionSejour();
$prescription_id = $sejour->_ref_prescription_sejour->_id;

$lines = array();

if($prescription_id){
	// Chargement des lignes de medicaments perop
	$prescription_line_medicament = new CPrescriptionLineMedicament();
	$prescription_line_medicament->prescription_id = $prescription_id;
	$prescription_line_medicament->perop = 1;
	$meds = $prescription_line_medicament->loadMatchingList();
	
	// Chargement des lignes d'elements perop
	$prescription_line_element = new CPrescriptionLineElement();
	$prescription_line_element->prescription_id = $prescription_id;
	$prescription_line_element->perop = 1;
	$elts = $prescription_line_element->loadMatchingList();
	
	// Chargement des lignes mix perop
	$prescription_line_mix = new CPrescriptionLineMix();
	$prescription_line_mix->prescription_id = $prescription_id;
	$prescription_line_mix->perop = 1;
	$mixes = $prescription_line_mix->loadMatchingList();
	
	foreach($meds as $_med){
		$_med->loadBackRefs("planifications");
		foreach($_med->_back["planifications"] as $_planif){
			$_planif->_quantite_adm = 0;
			$_planif->loadRefsAdministrations();
			foreach($_planif->_ref_administrations as $_adm){
				$_planif->_quantite_adm += $_adm->quantite;
			}
			$_planif->loadRefPrise();
			$_planif->_ref_object = $_med;
	    $lines["$_planif->dateTime-$_med->_guid"] = $_planif;
		}
	}
	
	foreach($elts as $_elt){
		$_elt->loadBackRefs("planifications");
		foreach($_elt->_back["planifications"] as $_planif){
			$_planif->_quantite_adm = 0;
			$_planif->loadRefsAdministrations();
			foreach($_planif->_ref_administrations as $_adm){
        $_planif->_quantite_adm += $_adm->quantite;
      }
	    $_planif->loadRefPrise();
			$_planif->_ref_object = $_elt;
	    $lines["$_planif->dateTime-$_elt->_guid"] = $_planif;
		}
	}
	
	foreach($mixes as $_mix){
		$_mix->loadRefsLines();
		foreach($_mix->_ref_lines as $_mix_item){
			$_mix_item->loadbackRefs("planifications");
			foreach($_mix_item->_back["planifications"] as $_planif){
				$_planif->_quantite_adm = 0;
				$_planif->loadRefsAdministrations();
				foreach($_planif->_ref_administrations as $_adm){
          $_planif->_quantite_adm += $_adm->quantite;
        }
	      $_planif->loadRefPrise();
		    $_planif->_ref_object = $_mix_item;
		    $lines["$_planif->dateTime-$_mix_item->_guid"] = $_planif;
			}
		}
	}
	
ksort($lines);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("sejour_id", $sejour_id);
$smarty->display("inc_vw_perop.tpl");

?>