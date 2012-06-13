<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

$only_sejour = CValue::get("only_sejour", 0);
$operation_id = CValue::get("operation_id", 0);

$sejour = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();
$sejour->loadRefsOperations();
$sejour->canRead();

if (!$only_sejour) {
  $consult_anesth = $sejour->loadRefsConsultAnesth();
  $consult_anesth->loadRefsFwd();
  
  foreach ($sejour->_ref_operations as $key=>$_operation) {
    if ($operation_id && $_operation->_id != $operation_id) {
      unset($sejour->_ref_operations[$key]);
      continue;
    }
    
  	$_operation->loadRefPlageOp();
  	$consult_anesth = $_operation->loadRefsConsultAnesth();
  	$consult_anesth->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("only_sejour", $only_sejour);
$smarty->assign("operation_id", $operation_id);

$smarty->display("inc_documents_sejour.tpl");
?>