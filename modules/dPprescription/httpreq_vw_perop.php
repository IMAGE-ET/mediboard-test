<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");
$operation_id = CValue::getOrSession("operation_id");

$operation = new COperation();
$operation->load($operation_id);

$sejour = new CSejour();
$sejour->load($sejour_id);

$prescription = $sejour->loadRefPrescriptionSejour();

$lines = array();

if($prescription->_id){
  $lines = $prescription->loadPeropLines();
}

// Chargement des anesths
$anesth = new CMediusers();
$anesths = $anesth->loadAnesthesistes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("prescription_id", $prescription->_id);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("anesths", $anesths);
$smarty->assign("operation", $operation);
$smarty->display("inc_vw_perop.tpl");

?>