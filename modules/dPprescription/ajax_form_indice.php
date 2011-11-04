<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$indice_cout_id = CValue::getOrSession("indice_cout_id");
$element_prescription_id = CValue::getOrSession("element_prescription_id");

$indice_cout = new CIndiceCout;
if ($indice_cout_id) {
  $indice_cout->load($indice_cout_id);
  $indice_cout->loadRefRessourceSoin();
}
else {
  $indice_cout->element_prescription_id = $element_prescription_id;
}

$ressource = new CRessourceSoin;
$ressources = $ressource->loadList();

$smarty = new CSmartyDP;

$smarty->assign("indice_cout", $indice_cout);
$smarty->assign("ressources", $ressources);

$smarty->display("inc_form_indice.tpl");

?>