<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$operation_id  = mbGetValueFromGetOrSession("operation_id", null);

$operation = new COperation;
if($operation->load($operation_id)) {
  $operation->loadRefChir();
  $operation->loadRefSejour();
  $operation->_ref_sejour->loadRefsFwd();
  $operation->loadRefPlageOp();
  $operation->loadRefsActesCCAM();
  foreach ($operation->_ref_actes_ccam as $keyActe => $valueActe) {
    $acte =& $operation->_ref_actes_ccam[$keyActe];
    $acte->loadRefsFwd();
    $acte->guessAssociation();
  }
}  


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"  , $operation->_ref_sejour->_ref_patient);
$smarty->assign("operation", $operation);

$smarty->display("print_feuille_bloc.tpl");
?>