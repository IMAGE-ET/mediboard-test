<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPlanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$sejour_id    = CValue::getOrSession("sejour_id");
$patient_id   = CValue::get("patient_id");
$praticien_id = CValue::get("praticien_id");
$grossesse_id = CValue::get("grossesse_id");
$dialog       = CValue::get("dialog", 0);

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// L'utilisateur est-il un praticien
$mediuser = CMediusers::get();
if ($mediuser->isPraticien() and !$praticien_id) {
  $praticien_id = $mediuser->user_id;
}

// Chargement du praticien
$praticien = new CMediusers;
if ($praticien_id) {
  $praticien->load($praticien_id);
}

// Chargement du patient
$patient = new CPatient;
if ($patient_id) {
  $patient->load($patient_id);
}

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);
$categorie_prat = array();
foreach($listPraticiens as $keyPrat =>$prat){
  $prat->loadRefsFwd();
  $categorie_prat[$keyPrat] = $prat->_ref_discipline->categorie;
}

// On récupére le séjour
$sejour = new CSejour;
if ($sejour_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefs();
  
  // On vérifie que l'utilisateur a les droits sur le sejour
  if (!$sejour->canRead()) {
    global $m, $tab;
    CAppUI::setMsg("Vous n'avez pas accés à ce séjour", UI_MSG_WARNING);
    CAppUI::redirect("m=$m&tab=$tab&sejour_id=0");
  }

  foreach ($sejour->_ref_operations as &$operation) {
    $operation->loadRefsFwd();
    $operation->_ref_chir->loadRefsFwd();
  }

  foreach ($sejour->_ref_affectations as &$affectation) {
    $affectation->loadRefLit();
    $lit =& $affectation->_ref_lit;
    $lit->loadCompleteView();
  }

  $praticien =& $sejour->_ref_praticien;
  $patient =& $sejour->_ref_patient;
}

$sejour->makeDatesOperations();
$sejour->loadRefsNotes();
$sejour->loadRefsConsultAnesth();
$sejour->_ref_consult_anesth->loadRefConsultation();

if (CModule::getActive("maternite")) {
  if ($grossesse_id) {
    $sejour->grossesse_id = $grossesse_id;
  }
  
  $sejour->loadRefGrossesse();
  
  if (!$sejour->_id && $grossesse_id) {
    $sejour->type_pec = 'O';
    $sejour->_date_entree_prevue = mbDate();
    $duree_sejour = CAppUI::conf("maternite duree_sejour");
    $sejour->_date_sortie_prevue = mbDate("+ $duree_sejour days");
    $sejour->_duree_prevue = $duree_sejour;
  }
}

$patient->loadRefsSejours();

if (count($patient->_ref_sejours))
  foreach($patient->_ref_sejours as $_sejour) {
    $_sejour->loadNDA();
    $_sejour->loadRefPraticien();
    $_sejour->loadRefEtablissement();
  }

$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();

$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
	$correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}
foreach($patient->_ref_medecins_correspondants as $correspondant) {
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
$where["externe"]  = "= '0'";
$service = new CService;
$services = $service->loadGroupList($where);

// Compter les prestations journalières système expert
$count_prestations = CPrestationJournaliere::countCurrentList();

// Chargement des prestations système standard
$prestations = CPrestation::loadCurrentList();

$sortie_sejour = mbDateTime();
if ($sejour->sortie_reelle) {
  $sortie_sejour = $sejour->sortie_reelle;
}

// Mise à disposition de lits
$affectation = new CAffectation();
$where = array();
$where["entree"] = "<= '$sortie_sejour'";
$where["sortie"] = ">= '$sortie_sejour'";
$where["function_id"] = "IS NOT NULL";
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";
foreach($blocages_lit as $blocage) {
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";
  if (!$sejour->_id && $affectation->loadObject($where)) {
    $affectation->loadRefSejour()->loadRefPatient();
    $blocage->_ref_lit->_view .= " indisponible jusqu'à ".mbTransformTime($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y")." (".$affectation->_ref_sejour->_ref_patient->_view.")";
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("urgInstalled", CModule::getInstalled("dPurgences"));
$smarty->assign("heure_sortie_ambu"   , $heure_sortie_ambu);
$smarty->assign("heure_sortie_autre"  , $heure_sortie_autre);
$smarty->assign("heure_entree_veille" , $heure_entree_veille);
$smarty->assign("heure_entree_jour"   , $heure_entree_jour);
//$smarty->assign("locked_sejour"         , $locked_sejour);

$smarty->assign("categorie_prat", $categorie_prat);
$smarty->assign("sejour"        , $sejour);
$smarty->assign("op"            , new COperation);
$smarty->assign("praticien"     , $praticien);
$smarty->assign("patient"       , $patient);
$smarty->assign("sejours"       , $sejours);

$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("count_etab_externe", $count_etab_externe);
$smarty->assign("medecin_adresse_par", $medecin_adresse_par);

$smarty->assign("etablissements", $etablissements);
$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("listServices"  , $services);

$smarty->assign("prestations"   , $prestations);
$smarty->assign("count_prestations", $count_prestations);

$smarty->assign("hours", $hours);
$smarty->assign("mins" , $mins);
$smarty->assign("blocages_lit" , $blocages_lit);

$smarty->assign("dialog", $dialog);

$smarty->display("vw_edit_sejour.tpl");

?>