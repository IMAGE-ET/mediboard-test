<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id    = mbGetValueFromGet("praticien_id");
$prescription_id = mbGetValueFromGet("prescription_id");

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefCurrentPraticien();

// Initialisations
$packs_praticien = array();
$packs_function = array();

// Chargement des protocoles du praticiens
$protocole = new CPrescription();
$where = array();
$where["praticien_id"] = " = '$praticien_id'";
$where["object_id"] = "IS NULL";
$where["object_class"] = " = '$prescription->object_class'";
$where["type"] = " = '$prescription->type'";
$protocoles_praticien = $protocole->loadList($where, "libelle");

// Chargement des packs du praticien
$pack_praticien = new CPrescriptionProtocolePack();
$pack_praticien->praticien_id = $praticien_id;
$pack_praticien->object_class = $prescription->object_class;
$packs_praticien = $pack_praticien->loadMatchingList();

// Chargement des protocoles de la fonction
$function_id = $praticien->function_id;
$where = array();
$where["function_id"] = " = '$function_id'";
$where["object_id"] = "IS NULL";
$where["object_class"] = " = '$prescription->object_class'";
$where["type"] = " = '$prescription->type'";
$protocoles_function = $protocole->loadList($where, "libelle");

// Chargement des packs de la fonction
$pack_function = new CPrescriptionProtocolePack(); 
$pack_function->function_id = $praticien->function_id;
$pack_function->object_class = $prescription->object_class;
$packs_function = $pack_function->loadMatchingList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("protocoles_praticien", $protocoles_praticien);
$smarty->assign("protocoles_function", $protocoles_function);
$smarty->assign("packs_praticien", $packs_praticien);
$smarty->assign("packs_function", $packs_function);
$smarty->assign("praticien", $praticien);
$smarty->display("../../dPprescription/templates/inc_select_protocole.tpl");

?>