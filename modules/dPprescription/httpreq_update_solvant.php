<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_mix_item_id = CValue::get("prescription_line_mix_item_id");
$modif_qte_totale = CValue::get("modif_qte_totale");

$prescription_line_mix_item = new CPrescriptionLineMixItem();
$prescription_line_mix_item->load($prescription_line_mix_item_id);

$prescription_line_mix_item->loadRefPerfusion();
$prescription_line_mix =& $prescription_line_mix_item->_ref_prescription_line_mix;

$quantite_solvant = $prescription_line_mix->quantite_totale; 
  
if($prescription_line_mix->quantite_totale){
	// Calcul de la quantite totale
  $prescription_line_mix->calculQuantiteTotal();
	
  // Calcul de la quantite de solvant
	if(!$modif_qte_totale){
		foreach($prescription_line_mix->_ref_lines as $_perf_line){
			if(!$_perf_line->solvant){
				$_unite_prise = str_replace('/kg', '', $_perf_line->unite);
				if(isset($_perf_line->_ref_produit->rapport_unite_prise[$_unite_prise]["ml"])){
				  $quantite_solvant -= $_perf_line->_quantite_administration; 
				}
			}
		}
	}
}

// Passage de la quantite à mettre à jour (quantite de solvant ou quantite totale)
if($modif_qte_totale){
	echo $prescription_line_mix->_quantite_totale;
} else {
  $prescription_line_mix_item->loadRefsFwd();
	if(in_array("ml", $prescription_line_mix_item->_unites_prise)){
		if($quantite_solvant < 0){
			$quantite_solvant = 0;
		}
		echo $quantite_solvant;
  }
}

?>