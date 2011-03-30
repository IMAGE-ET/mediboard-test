<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_mix_item_id = CValue::get("prescription_line_mix_item_id");

$line_mix_item = new CPrescriptionLineMixItem;
$line_mix_item->load($prescription_line_mix_item_id);

$line_mix_item->loadRefPerfusion();

$ref_line = $line_mix_item->_ref_prescription_line_mix;
$ref_line->calculQuantiteTotal();

if (!$ref_line->_debit) {
 echo "{debit: '-'}";
 return;
}
$duree = $ref_line->_quantite_totale / $ref_line->_debit;

$line_mix_item->updateQuantiteAdministration();

$debit = $line_mix_item->_quantite_administration / $duree;
$line_mix_item->loadRefProduit();

$produit = $line_mix_item->_ref_produit;
$produit->loadRapportUnitePriseByCIS();

$unite = str_replace('/kg', '', $line_mix_item->unite);

if ($unite != "ml") {
  $rapport = $produit->rapport_unite_prise[$unite];
  $conversion = reset($rapport);
  $unite = key($rapport);
}

// 2 chiffres significatifs
$debit = round($debit, 2);
echo "{debit: '".$debit . " $unite/h'}";

?>