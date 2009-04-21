<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $can;
$can->needsRead();

$sejour = new CSejour();
$sejour->load(mbGetValueFromGet("sejour_id"));
$sejour->loadRefsOperations();
foreach ($sejour->_ref_operations as $_operation) {
	$_operation->loadRefPlageOp();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);

$smarty->display("inc_documents_sejour.tpl");
?>