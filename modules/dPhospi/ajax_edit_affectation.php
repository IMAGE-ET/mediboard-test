<?php /* $Id: ajax_edit_affectations.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$affectation_id = CValue::get("affectation_id");
$lit_id         = CValue::get("lit_id");
$urgence         = CValue::get("urgence");

$affectation = new CAffectation();
$affectation->load($affectation_id);
$lit = new CLit();
$lit->load($affectation->lit_id);
if ($urgence) {
  $service_urgence = CGroups::loadCurrent()->service_urgences_id;
  $affectation->function_id = $service_urgence;
}

$sejour_maman = null;

if (CModule::getActive("maternite") && !$affectation->function_id) {
  $naissance = new CNaissance;
  $naissance->sejour_enfant_id = $affectation->sejour_id;
  $naissance->loadMatchingObject();
  
  if ($naissance->_id) {
    $sejour_maman = $naissance->loadRefSejourMaman();
    $sejour_maman->loadRefPatient();
  }
}

if ($affectation->_id) {
  $affectation->loadRefSejour()->loadRefPatient();
}
else {
  $affectation->lit_id = $lit_id;
  $lit->load($lit_id);
  $lit->loadRefChambre()->loadRefService();
  $affectation->entree = CMbDT::dateTime();
}

$smarty = new CSmartyDP;

$smarty->assign("affectation" , $affectation);
$smarty->assign("lit"         , $lit);
$smarty->assign("lit_id"      , $lit_id);
$smarty->assign("sejour_maman", $sejour_maman);
$smarty->assign("urgence"     , $urgence);

$smarty->display("inc_edit_affectation.tpl");
