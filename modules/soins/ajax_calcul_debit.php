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

$line_item = $line->_ref_lines[CValue::get("line_item_id")];
$quantite_produit = $line_item->_quantite_ml / $line_item->_ref_produit->rapport_unite_prise["mg"]["ml"];

$smarty->assign("line"            , $line);
$smarty->assign("line_item"       , $line_item);

$smarty->assign("poids"           , CValue::get("poids"));                                     // Poids du patient en kg
$smarty->assign("volume_total"    , CValue::get("volume_total"    , $line->_quantite_totale)); // Volume total en ml
$smarty->assign("quantite_produit", CValue::get("quantite_produit", $quantite_produit));       // Quantité du produit en mg
$smarty->display("ajax_calcul_debit.tpl");

