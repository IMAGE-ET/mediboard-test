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

$line_mix = $line_mix_item->_ref_prescription_line_mix;
$line_mix->calculQuantiteTotal();

if (!$line_mix->_debit || !$line_mix->_quantite_totale) {
 echo "{debit: '-'}";
 return;
}
$duree = $line_mix->_quantite_totale / $line_mix->_debit;

$line_mix_item->updateQuantiteAdministration();

$unite = $line_mix_item->unite;

$rapport_unite_prise = $line_mix_item->_ref_produit->rapport_unite_prise;

if ($rapport_unite_prise == null) {
  echo "{debit: '-'}";
  return;
}

$qte_admin = $line_mix_item->_quantite_administration;

if (array_key_exists("mg", $rapport_unite_prise)) {
  $qte_admin = $qte_admin / $rapport_unite_prise["mg"][$line_mix_item->_unite_administration];
  $unite = "mg";
}

$debit = $qte_admin / $duree;
$line_mix_item->loadRefProduit();

$produit = $line_mix_item->_ref_produit;
$produit->loadRapportUnitePriseByCIS();

if ($unite != "mg") {
  $unite = str_replace('/kg', '', $line_mix_item->unite);
  if ($unite != "ml") {
    $rapport = $produit->rapport_unite_prise[$unite];
    $conversion = reset($rapport);
    $unite = key($rapport);
  }
}

// 2 chiffres significatifs
$debit = round($debit, 2);
echo "{debit: '".$debit . " $unite/h'}";

?>