<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Smarty template
$smarty = new CSmartyDP();

$line_id      = CValue::get("line_id");
$line = new CPrescriptionLineMix();
$line->load($line_id);
$line->calculQuantiteTotal();

$line_item = new CPrescriptionLineMixItem();
$line_item = $line->_ref_lines[CValue::get("line_item_id")];
if($line_item->_quantite_ml) {
  if (array_key_exists("mg", $line_item->_ref_produit->rapport_unite_prise)) {
    $quantite_produit = $line_item->_quantite_ml / $line_item->_ref_produit->rapport_unite_prise["mg"]["ml"];
  } elseif(array_key_exists("�g", $line_item->_ref_produit->rapport_unite_prise)) {
    $quantite_produit = $line_item->_quantite_ml / $line_item->_ref_produit->rapport_unite_prise["�g"]["ml"] / 1000;
  }
} else {
  $quantite_produit = $line_item->quantite * $line_item->_ref_produit->rapport_unite_prise[$line_item->unite]["mg"];
}

$smarty->assign("line"            , $line);
$smarty->assign("line_item"       , $line_item);

$smarty->assign("poids"           , CValue::get("poids"));                                     // Poids du patient en kg
$smarty->assign("volume_total"    , CValue::get("volume_total"    , $line->_quantite_totale)); // Volume total en ml
$smarty->assign("quantite_produit", CValue::get("quantite_produit", $quantite_produit));       // Quantit� du produit en mg
$smarty->display("ajax_calcul_debit.tpl");

