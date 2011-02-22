<?php /* $Id: do_delivery_aed.php 6067 2009-04-14 08:04:15Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6067 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("poids"           , CValue::get("poids"));            // Poids du patient en kg
$smarty->assign("volume_total"    , CValue::get("volume_total"));     // Volume total en ml
$smarty->assign("quantite_produit", CValue::get("quantite_produit")); // Quantité du produit en mg
$smarty->display("ajax_calcul_debit.tpl");

