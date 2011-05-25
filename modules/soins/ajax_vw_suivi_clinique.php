<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->canRead();
$sejour->loadRelPatient();
$sejour->_ref_patient->loadRefPhotoIdentite();
$sejour->_ref_patient->loadRefsNotes();
$sejour->loadRefPraticien();
$sejour->loadRefsOperations();

foreach($sejour->_ref_operations as $_operation) {
  $_operation->loadRefsFwd();
  $_operation->_ref_chir->loadRefFunction();
}

$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();

$smarty = new CSmartyDP;
$smarty->assign("sejour", $sejour);

$smarty->display("inc_vw_suivi_clinique.tpl");

?>