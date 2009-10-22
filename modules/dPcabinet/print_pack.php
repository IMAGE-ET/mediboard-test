<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// !! Attention, régression importante si ajout de type de paiement

global $AppUI, $can, $m;

// Récupération des paramètres
$operation_id = mbGetValueFromGet("operation_id", null);
$op = new COperation;
$op->load($operation_id);
$op->loadRefsFwd();
$op->_ref_sejour->loadRefsFwd();
$patient =& $op->_ref_sejour->_ref_patient;

$pack_id = mbGetValueFromGet("pack_id", null);

$pack = new CPack;
$pack->load($pack_id);

// Creation des template manager
$listCr = array();
foreach($pack->_modeles as $key => $value) {
  $listCr[$key] = new CTemplateManager;
  $listCr[$key]->valueMode = true;
  $op->fillTemplate($listCr[$key]);
  $patient->fillTemplate($listCr[$key]);
  $listCr[$key]->applyTemplate($value);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCr", $listCr);

$smarty->display("print_pack.tpl");

?>