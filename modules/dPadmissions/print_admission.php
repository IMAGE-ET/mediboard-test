<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$id = CValue::getOrSession("id");

$admission = new CSejour();
$admission->load($id);
$admission->loadRefs();
$admission->loadNumDossier();
$admission->_ref_patient->loadRefsFwd();
$admission->_ref_patient->loadIPP();
foreach($admission->_ref_operations as $keyOp => $op) {
  $admission->_ref_operations[$keyOp]->loadRefsFwd();
  $admission->_ref_operations[$keyOp]->loadExtCodesCCAM();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("admission", $admission);

if(CAppUI::conf("dPadmissions fiche_admission") == "a4") {
  $smarty->display("print_admission.tpl");
} else {
  $smarty->display("print_admission_A5.tpl");
}

?>