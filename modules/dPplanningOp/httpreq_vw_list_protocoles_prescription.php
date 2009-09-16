<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Fabien Ménager
*/

$praticien_id = mbGetValueFromGetOrSession("praticien_id", 0);
$selected_id = mbGetValueFromGetOrSession("selected_id", 0);
$without_pack = mbGetValueFromGet("without_pack");

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
$protocoles_list_praticien = $prescription->loadList($where, "libelle");

// Chargement des packs du praticien
$pack_praticien = new CPrescriptionProtocolePack();
$pack_praticien->praticien_id = $praticien_id;
$pack_praticien->object_class = 'CSejour';
$packs_praticien = $pack_praticien->loadMatchingList("libelle");
  
$where["function_id"] = "= '$praticien->function_id'";
unset($where["praticien_id"]);

$protocoles_list_function = $prescription->loadList($where, "libelle");

 // Chargement des packs de la fonction
$pack_function = new CPrescriptionProtocolePack(); 
$pack_function->function_id = $praticien->function_id;
$pack_function->object_class = 'CSejour';
$packs_function = $pack_function->loadMatchingList("libelle");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selected_id", $selected_id);
$smarty->assign("protocoles_list_praticien", $protocoles_list_praticien);
$smarty->assign("protocoles_list_function", $protocoles_list_function);
$smarty->assign("packs_praticien", $packs_praticien);
$smarty->assign("packs_function", $packs_function);
$smarty->assign("nodebug", true);
$smarty->assign("without_pack", $without_pack);

$smarty->display("inc_vw_list_protocoles_prescription.tpl");

?>