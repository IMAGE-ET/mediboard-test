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
$sejour->loadRefsTransmissions(true);

// Tri des transmissions par catégorie
$transmissions = array();

foreach ($sejour->_ref_transmissions as $_trans) {
  $_trans->loadTargetObject();
  $nom = $_trans->_ref_object->nom;
  if (!isset($transmissions[$nom])) {
    $transmissions[$nom] = array();
  }
  $transmissions[$nom][] = $_trans;
}

$sejour->_ref_transmissions = $transmissions;

foreach($sejour->_ref_operations as $_operation) {
  $_operation->loadRefsFwd();
  $_operation->_ref_chir->loadRefFunction();
}

$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();
$prescription_sejour = $sejour->loadRefPrescriptionSejour();

// Chargement des lignes de prescriptions
$prescription_sejour->loadRefsLinesMedComments("1", "debut ASC", "", 24);
$prescription_sejour->loadRefsLinesElementsComments("1","","debut ASC", "", 24);

// Chargement des prescription_line_mixes
$prescription_sejour->loadRefsPrescriptionLineMixes("", 0, 1, "", 24);

foreach($prescription_sejour->_ref_prescription_line_mixes as $curr_prescription_line_mix){
  $curr_prescription_line_mix->loadRefsLines();
}

$date = mbDateTime();
$date_before = mbDateTime("-24 hours", $date);
$date_after  = mbDateTime("+24 hours", $date);

$smarty = new CSmartyDP;
$smarty->assign("sejour", $sejour);
$smarty->assign("date"  , $date);
$smarty->assign("date_before"  , $date_before);
$smarty->assign("date_after"   , $date_after);
$smarty->display("inc_vw_suivi_clinique.tpl");

?>