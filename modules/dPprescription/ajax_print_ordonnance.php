<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id     = CValue::getOrSession("prescription_id");
$praticien_sortie_id = CValue::get("praticien_sortie_id");

$prescription = new CPrescription;
$prescription->load($prescription_id);

$current_user = CAppUI::$user;
$praticiens = $current_user->loadPraticiens(PERM_EDIT);

$praticien = new CMediusers();

if ($praticien_sortie_id){
  $praticien->load($praticien_sortie_id); 
}
else if ($current_user->isPraticien()) {
  $praticien = $current_user;
  $praticien_sortie_id = $praticien->_id;
}

$smarty = new CSmartyDP;

$smarty->assign('prescription', $prescription);
$smarty->assign("praticien_sortie_id", $praticien_sortie_id);
$smarty->assign("praticiens", $praticiens);

$smarty->display("inc_print_ordonnance.tpl");
?>