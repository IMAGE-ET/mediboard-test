<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$perfusion_line_id = CValue::get("perfusion_line_id");
$modif_qte_totale = CValue::get("modif_qte_totale");

$perfusion_line = new CPerfusionLine();
$perfusion_line->load($perfusion_line_id);

$perfusion_line->loadRefPerfusion();
$perfusion =& $perfusion_line->_ref_perfusion;

$quantite_solvant = $perfusion->quantite_totale; 
  
if($perfusion->quantite_totale){
	// Calcul de la quantite totale
  $perfusion->calculQuantiteTotal();
	
  // Calcul de la quantite de solvant
	if(!$modif_qte_totale){
		foreach($perfusion->_ref_lines as $_perf_line){
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
	echo $perfusion->_quantite_totale;
} else {
  $perfusion_line->loadRefsFwd();
	if(in_array("ml", $perfusion_line->_unites_prise)){
		if($quantite_solvant < 0){
			$quantite_solvant = 0;
		}
		echo $quantite_solvant;
  }
}

?>