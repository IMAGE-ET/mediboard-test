<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$id = mbGetValueFromGetOrSession("id");

$admission = new CSejour();
$admission->load($id);
$admission->loadRefs();
$admission->_ref_patient->loadRefsFwd();
foreach($admission->_ref_operations as $keyOp => $op) {
  $admission->_ref_operations[$keyOp]->loadRefsFwd();
  $admission->_ref_operations[$keyOp]->loadRefCCAM();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("admission", $admission);

$smarty->display("print_admission.tpl");

?>