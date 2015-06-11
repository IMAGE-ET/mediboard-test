<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();


$sejour_id    = CValue::getOrSession("sejour_id");
$patient_id   = CValue::get("patient_id");
$praticien_id = CValue::get("praticien_id");
$grossesse_id = CValue::get("grossesse_id");
$dialog       = CValue::get("dialog", 0);
$consult_related_id = CValue::get("consult_related_id");

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// L'utilisateur est-il un praticien
$mediuser = CMediusers::get();
if ($mediuser->isPraticien() and !$praticien_id) {
  $praticien_id = $mediuser->user_id;
}

// Chargement du praticien
$praticien = new CMediusers();
if ($praticien_id) {
  $praticien->load($praticien_id);
}

// Chargement du patient
$patient = new CPatient();
if ($patient_id) {
  $patient->load($patient_id);
}

// On récupére le séjour
$sejour = new CSejour();
$sejour->_ref_patient = $patient;
$sejour->consult_related_id = $consult_related_id;

if ($sejour_id) {
  $sejour->load($sejour_id);

  if (CBrisDeGlace::isBrisDeGlaceRequired()) {
    $canAccess = CAccessMedicalData::checkForSejour($sejour);
    if (!$canAccess) {
      if (!$sejour->canDo()->read) {
        global $m, $tab;
        CAppUI::setMsg("Vous n'avez pas accés à ce séjour", UI_MSG_WARNING);
        CAppUI::redirect("m=$m&tab=$tab&sejour_id=0");
      }
    }
  }
  else {
    if (!$sejour->canDo()->read) {
      global $m, $tab;
      CAppUI::setMsg("Vous n'avez pas accés à ce séjour", UI_MSG_WARNING);
      CAppUI::redirect("m=$m&tab=$tab&sejour_id=0");
    }
  }

  $sejour->loadRefPatient();
  $sejour->loadRefPraticien()->canDo();
  $sejour->loadRefEtablissement();
  $sejour->loadRefEtablissementTransfert();
  $sejour->loadRefServiceMutation();
  $sejour->loadRefsAffectations();
  $sejour->loadRefsOperations();
  $sejour->loadRefCurrAffectation()->loadRefService();

  foreach ($sejour->_ref_operations as $operation) {
    $operation->loadRefPlageOp();
    $operation->loadExtCodesCCAM();
    $operation->loadRefsConsultAnesth();
    $operation->loadRefChir()->loadRefFunction();
    $operation->loadRefPatient();
    $operation->_ref_chir->loadRefFunction();
    $operation->_ref_chir->loadRefSpecCPAM();
    $operation->_ref_chir->loadRefDiscipline();
    $operation->loadBrancardage();
  }

  foreach ($sejour->_ref_affectations as $affectation) {
    $affectation->loadView();
  }
  $praticien = $sejour->_ref_praticien;
  $patient = $sejour->_ref_patient;
}

$sejour->makeDatesOperations();
$sejour->loadRefsNotes();
$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();


if (CModule::getActive("reservation") && !$sejour_id && $dialog) {
  $date_reservation = CValue::get("date_reservation");
  $sejour->_date_entree_prevue = $sejour->_date_sortie_prevue = $date_reservation;
}

if (CModule::getActive("maternite")) {
  if ($grossesse_id) {
    $sejour->grossesse_id = $grossesse_id;
  }
  
  $sejour->loadRefGrossesse();
  
  if (!$sejour->_id && $grossesse_id) {
    $sejour->type_pec = 'O';
    $sejour->_date_entree_prevue = CMbDT::date();
    $duree_sejour = CAppUI::conf("maternite duree_sejour");
    $sejour->_date_sortie_prevue = CMbDT::date("+ $duree_sejour days");
    $sejour->_duree_prevue = $duree_sejour;
    $sejour->type = $duree_sejour > 0 ? "comp" : "ambu";
  }
}

$patient->loadRefsSejours();
$patient->loadRefsCorrespondantsPatient();

if (count($patient->_ref_sejours)) {
  foreach ($patient->_ref_sejours as $_sejour) {
    $_sejour->loadNDA();
    $_sejour->loadRefPraticien();
    $_sejour->loadRefEtablissement();
  }
}

$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();

$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
  $correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}
foreach ($patient->_ref_medecins_correspondants as $correspondant) {
  $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
}

$medecin_adresse_par = "";
if ($sejour->adresse_par_prat_id && ($sejour->adresse_par_prat_id != $patient->_ref_medecin_traitant->_id)) {
  $medecin_adresse_par = new CMedecin();
  $medecin_adresse_par->load($sejour->adresse_par_prat_id);
}

$sejours =& $patient->_ref_sejours;

// Heures & minutes
$config = CAppUI::conf("dPplanningOp CSejour");

$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];

$sejour->makeCancelAlerts();
$sejour->loadNDA();

// Chargement des etablissements externes
$etab = new CEtabExterne();
$count_etab_externe = $etab->countList();

// Récupération de la liste des services
$where = array();
$where["externe"]    = "= '0'";
$where["cancelled"]  = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

foreach ($services as $_service) {
  $_service->loadRefUFSoins();
}

// Chargement des prestations système standard
$prestations = CPrestation::loadCurrentList();

$sortie_sejour = CMbDT::dateTime();
if ($sejour->sortie_reelle) {
  $sortie_sejour = $sejour->sortie_reelle;
}

// Mise à disposition de lits
$affectation = new CAffectation();
$where = array();
$where["entree"] = "<= '$sortie_sejour'";
$where["sortie"] = ">= '$sortie_sejour'";
$where["function_id"] = "IS NOT NULL";
/** @var CAffectation[] $blocages_lit */
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";
foreach ($blocages_lit as $blocage) {
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";
  if (!$sejour->_id && $affectation->loadObject($where)) {
    $affectation->loadRefSejour()->loadRefPatient();
    $blocage->_ref_lit->_view .= " indisponible jusqu'à ".CMbDT::transform($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y");
    $blocage->_ref_lit->_view .= " (".$affectation->_ref_sejour->_ref_patient->_view.")";
  }
}

$list_mode_sortie = array();
if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
  $mode_sortie = new CModeSortieSejour();
  $where = array(
    "actif" => "= '1'",
  );
  $list_mode_sortie = $mode_sortie->loadGroupList($where);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("urgInstalled", CModule::getInstalled("dPurgences"));
$smarty->assign("heure_sortie_ambu"   , $heure_sortie_ambu);
$smarty->assign("heure_sortie_autre"  , $heure_sortie_autre);
$smarty->assign("heure_entree_veille" , $heure_entree_veille);
$smarty->assign("heure_entree_jour"   , $heure_entree_jour);

$smarty->assign("sejour"        , $sejour);
$smarty->assign("op"            , new COperation());
$smarty->assign("praticien"     , $praticien);
$smarty->assign("patient"       , $patient);
$smarty->assign("sejours"       , $sejours);
$smarty->assign("ufs"           , CUniteFonctionnelle::getUFs());

$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("count_etab_externe"    , $count_etab_externe);
$smarty->assign("medecin_adresse_par"   , $medecin_adresse_par);

$smarty->assign("etablissements", $etablissements);
$smarty->assign("listServices"  , $services);

$smarty->assign("prestations"      , $prestations);

$smarty->assign("hours", $hours);
$smarty->assign("mins" , $mins);
$smarty->assign("blocages_lit" , $blocages_lit);

$smarty->assign("list_mode_sortie", $list_mode_sortie);

$smarty->assign("dialog", $dialog);

$smarty->display("../../dPplanningOp/templates/vw_edit_sejour.tpl");
