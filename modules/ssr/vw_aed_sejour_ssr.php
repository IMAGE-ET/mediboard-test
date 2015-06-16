<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$group_id = CGroups::loadCurrent()->_id;

$sejour_id = CValue::getOrSession("sejour_id");

$user = CMediusers::get();
$prats = $user->loadPraticiens(PERM_READ);

$service  = new CService();
$where    = array("group_id" => "= '$group_id'");
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ, $where, $order);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefsNotes();
$sejour->loadRefsDocItems();

if ($sejour_id && !$sejour->_id) {
  CAppUI::setMsg(CAppUI::tr("CSejour-unavailable"), UI_MSG_WARNING);
  CAppUI::redirect("m=ssr&tab=vw_aed_sejour&sejour_id=0");
}

$fiche_autonomie = new CFicheAutonomie;
$patient = new CPatient;
$bilan = new CBilanSSR;
$prescription = null;
$lines = array();
$medecin_adresse_par = "";
$correspondantsMedicaux = array();

if ($sejour->_id) {
  $sejour->loadRefPatient();
  $sejour->loadNDA();

  // Chargement du patient
  $patient = $sejour->_ref_patient;
  $patient->loadIPP();
  $patient->loadRefsCorrespondants();
  if ($sejour->adresse_par_prat_id && ($sejour->adresse_par_prat_id != $patient->_ref_medecin_traitant->_id)) {
    $medecin_adresse_par = new CMedecin();
    $medecin_adresse_par->load($sejour->adresse_par_prat_id);
  }

  // Fiche autonomie  
  $fiche_autonomie->sejour_id = $sejour->_id;
  $fiche_autonomie->loadMatchingObject();
  
  // Bilan SSR  
  $bilan->sejour_id = $sejour->_id;
  $bilan->loadMatchingObject();
  
  // Prescription SSR
  $prescription = $sejour->loadRefPrescriptionSejour();
    
  // Chargement des lignes de la prescription
  if ($prescription && $prescription->_id) {
    $line = new CPrescriptionLineElement();
    $line->prescription_id = $prescription->_id;
    /** @var CPrescriptionLineElement $_lines */
    $_lines = $line->loadMatchingList("debut ASC");
    foreach ($_lines as $_line) {
      $line->getRecentModification();
      $lines[$_line->_ref_element_prescription->category_prescription_id][$_line->element_prescription_id][] = $_line;
    }
  }
  
  if ($patient->_ref_medecin_traitant->_id) {
    $correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
  }
  
  foreach ($patient->_ref_medecins_correspondants as $correspondant) {
    $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
  }
}
else {
  $sejour->group_id = $group_id;
  $sejour->praticien_id = $user->_id;
  $sejour->entree_prevue = CMbDT::date()." 08:00:00";
  $sejour->sortie_prevue = CMbDT::date()." 18:00:00";
  $sejour->recuse = CAppUI::conf("ssr recusation use_recuse") ? -1 : 0;
}

// Chargement des categories de prescription
$categories = null;
if (CModule::getActive("dPprescription")) {
  $categories = array();
  $category = new CCategoryPrescription();
  $where = array();
  $where[] = "chapitre = 'kine'";
  $where[] = "group_id = '$group_id' OR group_id IS NULL";
  
  $order = "nom";
  $categories = $category->loadList($where, $order);
}

// Dossier médical visibile ?
$can_view_dossier_medical =
  $user->isMedical();

$can_edit_prescription = 
  $user->isPraticien() || 
  $user->isAdmin();

// Suppression des categories vides
if (!$can_edit_prescription) {
  foreach ($categories as $_cat_id => $_category) {
    if (!array_key_exists($_cat_id, $lines)) {
      unset($categories[$_cat_id]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("today"                   , CMbDT::date());
$smarty->assign("sejour"                  , $sejour);
$smarty->assign("fiche_autonomie"         , $fiche_autonomie);
$smarty->assign("bilan"                   , $bilan);
$smarty->assign("patient"                 , $patient);
$smarty->assign("prats"                   , $prats);
$smarty->assign("services"                , $services);
$smarty->assign("categories"              , $categories);
$smarty->assign("prescription"            , $prescription);
$smarty->assign("lines"                   , $lines);
$smarty->assign("medecin_adresse_par"     , $medecin_adresse_par);
$smarty->assign("can_edit_prescription"   , $can_edit_prescription);
$smarty->assign("correspondantsMedicaux"  , $correspondantsMedicaux);

$smarty->display("vw_aed_sejour_ssr.tpl");
