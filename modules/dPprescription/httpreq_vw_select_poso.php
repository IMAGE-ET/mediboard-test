<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$code_cip = CValue::get("code_cip");

$produit = new CBcbProduit();
if($code_cip && is_numeric($code_cip)){
  $produit->load($code_cip);
} else {
  return;
}

$line = new CPrescriptionLineMedicament();
$line->code_cip = $code_cip;
$line->loadRefsFwd();

$prise = new CPrisePosologie();
$prise->quantite = 1;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prise", $prise);
$smarty->assign("unites_prise", $line->_unites_prise);
$smarty->display("../../dPprescription/templates/inc_vw_select_poso_lite.tpl");

?>