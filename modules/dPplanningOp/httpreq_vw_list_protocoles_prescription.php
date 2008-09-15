<?php /* $Id: vw_edit_protocole.php 3902 2008-04-15 21:12:15Z mytto $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 3902 $
* @author Fabien Ménager
*/

global $can;

$can->needsEdit();

$praticien_id = mbGetValueFromGetOrSession("praticien_id", 0);
$selected_id = mbGetValueFromGetOrSession("selected_id", 0);

// Chargement du praticien
$praticien = new CMediusers;
if ($praticien_id) {
  $praticien->load($praticien_id);
}

// On charge la liste des protocoles de prescription
$prescription = new CPrescription();
$where = array();
$where["praticien_id"] = "= '$praticien->_id'";
$where["object_class"] = " = 'CSejour'";
$where["object_id"] = "IS NULL";
$protocoles_list_praticien = $prescription->loadList($where);

$where["function_id"] = "= '$praticien->function_id'";
$where["praticien_id"] = null;
$protocoles_list_function = $prescription->loadList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selected_id", $selected_id);
$smarty->assign("protocoles_list_praticien", $protocoles_list_praticien);
$smarty->assign("protocoles_list_function", $protocoles_list_function);

$smarty->assign("nodebug", true);

$smarty->display("inc_vw_list_protocoles_prescription.tpl");

?>