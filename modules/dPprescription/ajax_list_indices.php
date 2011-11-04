<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$element_prescription_id = CValue::getOrSession("element_prescription_id");
$indice_cout_id = CValue::getOrSession("indice_cout_id");
//mbTrace($_SESSION["dPprescription"]);
$element_prescription = new CElementPrescription;
$element_prescription->load($element_prescription_id);

$indices = $element_prescription->loadBackRefs("indices_cout");

foreach ($indices as $_indice) {
  $_indice->loadRefRessourceSoin();
}

$smarty = new CSmartyDP;

$smarty->assign("indices", $indices);
$smarty->assign("element_prescription_id", $element_prescription_id);
$smarty->assign("indice_cout_id" , $indice_cout_id);
$smarty->display("inc_list_indices.tpl");

?>