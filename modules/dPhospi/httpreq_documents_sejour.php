<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

$only_sejour = CValue::get("only_sejour", 0);

$sejour = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefsOperations();
$sejour->canRead();

if (!$only_sejour) {
  $consult_anesth = $sejour->loadRefsConsultAnesth();
  $consult_anesth->loadRefsFwd();
  
  foreach ($sejour->_ref_operations as $_operation) {
  	$_operation->loadRefPlageOp();
  	$consult_anesth = $_operation->loadRefsConsultAnesth();
  	$consult_anesth->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("only_sejour", $only_sejour);

$smarty->display("inc_documents_sejour.tpl");
?>