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
$sejour->loadRefCurrAffectation();
$sejour->_ref_curr_affectation->loadView();
$sejour->canRead();
$patient = $sejour->loadRelPatient();
$patient->loadRefsCorrespondantsPatient();
$patient->loadRefPhotoIdentite();
$patient->loadRefsNotes();
$dossier_medical = $patient->loadRefDossierMedical();

if ($dossier_medical->_id) {
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();
}

$sejour->loadRefPraticien();
$sejour->loadRefsOperations();

// Gestion des macro-cible seulement si prescription disponible
$cible_importante = CModule::getInstalled("dPprescription");
$sejour->loadRefsTransmissions($cible_importante, true);

$sejour->loadRefsObservations(true);
$sejour->loadRefsTasks();
$sejour->loadRefsNotes();

foreach ($sejour->_ref_tasks as $key=>$_task) {
  if ($_task->realise) {
    unset($sejour->_ref_tasks[$key]);
  }
  else {
    $_task->loadRefPrescriptionLineElement();
  }
}

// Tri des transmissions par catégorie
$transmissions = array();

foreach ($sejour->_ref_transmissions as $_trans) {
  $_trans->loadTargetObject();
  $nom = get_class($_trans->_ref_object) == "CCategoryPrescription" ? $_trans->_ref_object->nom : "Autres";
  if (!isset($transmissions[$nom])) {
    $transmissions[$nom] = array();
  }
  $transmissions[$nom][] = $_trans;
}

$sejour->_ref_transmissions = $transmissions;

foreach ($sejour->_ref_operations as $_operation) {
  $_operation->loadRefsFwd();
  $_operation->_ref_chir->loadRefFunction();
  $_operation->loadBrancardage();
}

$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();

if(CModule::getActive("dPprescription")){
	$prescription_sejour = $sejour->loadRefPrescriptionSejour();
	
	// Chargement des lignes de prescriptions
	$prescription_sejour->loadRefsLinesMedComments();
	$prescription_sejour->loadRefsLinesElementsComments();
	
	// Chargement des prescription_line_mixes
	$prescription_sejour->loadRefsPrescriptionLineMixes();
	
	foreach ($prescription_sejour->_ref_prescription_line_mixes as $curr_prescription_line_mix){
	  $curr_prescription_line_mix->loadRefsLines();
	  $curr_prescription_line_mix->_compact_view = array();
	  foreach ($curr_prescription_line_mix->_ref_lines as $_line) {
	    if (!$_line->solvant) {
	      $curr_prescription_line_mix->_compact_view[] = $_line->_ref_produit->libelle_abrege;
	    }
	  }
	  if (count($curr_prescription_line_mix->_compact_view)) {
	    $curr_prescription_line_mix->_compact_view = implode(", ", $curr_prescription_line_mix->_compact_view);
	  }
	  else {
	    $curr_prescription_line_mix->_compact_view = "";
	  }
  }
}

// Utilisateur ayant confirmé la sortie
$logConfirme = $sejour->loadLastLogForField("confirme");
$user_confirm_sortie = new CMediusers();
$user_confirm_sortie->load($logConfirme->user_id);
$user_confirm_sortie->loadRefFunction();

if (CModule::getActive("dPprescription")){
  $date = CMbDT::dateTime();
  $days_config = CAppUI::conf("dPprescription CPrescription nb_days_prescription_current");
  $date_before = CMbDT::dateTime("-$days_config DAY", $date);
  $date_after  = CMbDT::dateTime("+$days_config DAY", $date);
}

$smarty = new CSmartyDP;
$smarty->assign("sejour"             , $sejour);
$smarty->assign("user_confirm_sortie", $user_confirm_sortie);

if (CModule::getActive("dPprescription")){
	$smarty->assign("date"  , $date);
	$smarty->assign("days_config", $days_config);
	$smarty->assign("date_before"  , $date_before);
	$smarty->assign("date_after"   , $date_after);
}
$smarty->display("inc_vw_suivi_clinique.tpl");

?>