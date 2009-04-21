<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$id = mbGetValueFromGetOrSession("id");

$admission = new CSejour();
$admission->load($id);
$admission->loadRefs();
$admission->_ref_patient->loadRefsFwd();
foreach($admission->_ref_operations as $keyOp => $op) {
  $admission->_ref_operations[$keyOp]->loadRefsFwd();
  $admission->_ref_operations[$keyOp]->loadExtCodesCCAM();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("admission", $admission);

$smarty->display("print_admission.tpl");

?>