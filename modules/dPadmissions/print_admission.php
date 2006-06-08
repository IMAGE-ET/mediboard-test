<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

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
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('admission', $admission);

$smarty->display('print_admission.tpl');

?>