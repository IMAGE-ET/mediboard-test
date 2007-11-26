<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$operation_id = mbGetValueFromGetOrSession("operation_id");

// Chargement de l'operation
$operation = new COperation();
$operation->load($operation_id);

// Chargement des documents de l'operation
$operation->loadRefsDocs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp"   , $operation);

$smarty->display("inc_vw_list_documents.tpl");

?>