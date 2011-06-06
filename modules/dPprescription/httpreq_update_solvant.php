<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_mix_item_id = CValue::get("prescription_line_mix_item_id");
$prescription_line_mix_id = CValue::get("prescription_line_mix_id");
$quantite_solvant  = CValue::get("quantite_totale", null);
$modif_qte_totale  = CValue::get("modif_qte_totale");
$solvant           = CValue::get("solvant", true);

$prescription_line_mix = new CPrescriptionLineMix();
$prescription_line_mix->load($prescription_line_mix_id);

// Calcul de la quantite totale
$prescription_line_mix->calculQuantiteTotal();

$line_solvant = null;

if ($quantite_solvant){
  // Calcul de la quantite de solvant
  if($modif_qte_totale){
    foreach($prescription_line_mix->_ref_lines as $_perf_line){
      if(!$_perf_line->solvant){
        $_unite_prise = str_replace('/kg', '', $_perf_line->unite);
        if (isset($_perf_line->_ref_produit->rapport_unite_prise[$_unite_prise]["ml"]) || $_unite_prise == "ml") {
          $quantite_solvant -= $_perf_line->_quantite_administration;
        } 
      }
      else {
        $line_solvant = $_perf_line;
      }
    }
  }
}

if ($line_solvant) {
  if (preg_match("/([0-9\.]+)\s*ml/", $line_solvant->unite, $matches)) {
    $quantite_solvant /= $matches[1];
  }
}

// Passage de la quantite à mettre à jour (quantite de solvant ou quantite totale)
if ($modif_qte_totale && $solvant){
	echo round($prescription_line_mix->_quantite_totale, 2);
}
else {
  $prescription_line_mix_item = new CPrescriptionLineMixItem();
  $prescription_line_mix_item->load($prescription_line_mix_item_id);
  $prescription_line_mix_item->loadRefsFwd();
	if(in_array("ml", $prescription_line_mix_item->_unites_prise)){
		if($quantite_solvant < 0){
			$quantite_solvant = 0;
		}
		echo round($quantite_solvant, 2);
  }
}

?>