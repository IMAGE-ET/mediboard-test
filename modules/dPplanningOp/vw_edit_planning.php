<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();

// Liste des Etablissements selon Permissions
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

$operation_id = CValue::getOrSession("operation_id");
$chir_id      = CValue::getOrSession("chir_id");
$sejour_id    = CValue::get("sejour_id");
$patient_id   = CValue::get("pat_id");
$today        = mbDate();
$tomorow      = mbDate("+1 DAY");

// L'utilisateur est-il un praticien
$user = CAppUI::$user;
if ($user->isPraticien() and !$chir_id) {
  $chir_id = $user->_id;
}

// Chargement du praticien
$chir = new CMediusers;
if ($chir_id) {
  $testChir = new CMediusers();
  $testChir->load($chir_id);
  if($testChir->isPraticien()) {
    $chir = $testChir;
  }
}
$chir->loadRefFunction();
$prat = $chir;

// Chargement du patient
$patient = new CPatient;
if ($patient_id && !$operation_id && !$sejour_id) {
  $patient->load($patient_id);
  $patient->loadRefsSejours();
}

// Vérification des droits sur les praticiens
if ($user->isAnesth()) {
  $listPraticiens = $chir->loadPraticiens(null);
} else {
  $listPraticiens = $chir->loadPraticiens(PERM_EDIT);
}

$categorie_prat = array();
foreach($listPraticiens as &$_prat){
  $_prat->loadRefsFwd();
  $categorie_prat[$_prat->_id] = $_prat->_ref_discipline->categorie;
}

// On récupère le séjour
$sejour = new CSejour;

if($sejour_id && !$operation_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  
  if(!$chir_id) {
    $chir = $sejour->_ref_praticien;
  }
  // On ne change a priori pas le praticien du séjour
  $prat    = $sejour->_ref_praticien;
  $patient = $sejour->_ref_patient;
}

// Liste des types d'anesthésie
$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);

// Liste des anesthésistes
$anesthesistes = $user->loadAnesthesistes(PERM_READ);

// On récupère l'opération
$op = new COperation;
$op->load($operation_id);
if ($op->_id){
  $op->loadRefs();
  $op->loadRefsNotes();
  $op->_ref_chir->loadRefFunction();
  
  foreach($op->_ref_actes_ccam as $acte) {
    $acte->loadRefExecutant();
  }

  $sejour =& $op->_ref_sejour;
  $sejour->loadRefsFwd();
  $sejour->makeCancelAlerts($op->_id);
  $chir =& $op->_ref_chir;
  $prat =& $sejour->_ref_praticien;
  
  $patient =& $sejour->_ref_patient;
  
  global $m, $tab;
//  // On vérifie que l'utilisateur a les droits sur l'operation et le sejour
//  if(!$op->canEdit() || !$sejour->canEdit()) {
//    CAppUI::setMsg("Vous n'avez pas accès à cette opération", UI_MSG_WARNING);
//    CAppUI::redirect("m=$m&tab=$tab&operation_id=0");
//  }
  
  // Ancienne methode
  /*if (!array_key_exists($op->chir_id, $listPraticiens)) {
    CAppUI::setMsg("Vous n'avez pas accès à cette opération", UI_MSG_WARNING);
    CAppUI::redirect("m=$m&tab=$tab&operation_id=0");
  }*/
}

CValue::setSession("chir_id", $chir->_id);

// Compléments de chargement du séjour
$sejour->makeDatesOperations();
$sejour->loadNDA();
$sejour->loadRefsNotes();

// Chargements de chargement du patient
$patient->loadRefsSejours();
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

// Chargement des etablissements externes
$etab = new CEtabExterne();
$count_etab_externe = $etab->countList();

$sejours =& $patient->_ref_sejours;

$config = CAppUI::conf("dPplanningOp CSejour");
$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];

$list_hours_voulu = range(7, 20);
$list_minutes_voulu = range(0, 59, $config["min_intervalle"]);

foreach ($list_minutes_voulu as &$minute){
  $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
}

$config = CAppUI::conf("dPplanningOp COperation");
$hours_duree = range($config["duree_deb"], $config["duree_fin"]);
$hours_urgence = range($config["hour_urgence_deb"], $config["hour_urgence_fin"]);
$mins_duree = range(0, 59, $config["min_intervalle"]);

// Récupération de la liste des services
$where = array();
$where["externe"]  = "= '0'";
$service = new CService;
$services = $service->loadGroupList($where);

// Compter les prestations journalières
$count_prestations = CPrestationJournaliere::countCurrentList();

$sortie_sejour = mbDateTime();
if ($sejour->sortie_reelle) {
  $sortie_sejour = $sejour->sortie_reelle;
}

$where = array();
$where["entree"] = "<= '".$sortie_sejour."'";
$where["sortie"] = ">= '".$sortie_sejour."'";
$where["function_id"] = "IS NOT NULL";

$affectation = new CAffectation();
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";

foreach($blocages_lit as $key => $blocage){
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";
  if(!$sejour->_id && $affectation->loadObject($where))
  {
    $affectation->loadRefSejour();
    $affectation->_ref_sejour->loadRefPatient();
    $blocage->_ref_lit->_view .= " indisponible jusqu'à ".mbTransformTime($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y")." (".$affectation->_ref_sejour->_ref_patient->_view.")";
  }
}

if (CModule::getActive("maternite")) {
  $sejour->loadRefGrossesse();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_collision", $patient->getSejoursCollisions());

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("urgInstalled", CModule::getInstalled("dPurgences"));
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("heure_sortie_ambu",   $heure_sortie_ambu);
$smarty->assign("heure_sortie_autre",  $heure_sortie_autre);
$smarty->assign("heure_entree_veille", $heure_entree_veille);
$smarty->assign("heure_entree_jour",   $heure_entree_jour);

$smarty->assign("op"        , $op);
$smarty->assign("plage"     , $op->plageop_id ? $op->_ref_plageop : new CPlageOp );
$smarty->assign("sejour"    , $sejour);
$smarty->assign("chir"      , $chir);
$smarty->assign("praticien" , $prat);
$smarty->assign("patient"   , $patient);
$smarty->assign("sejours"   , $sejours);
$smarty->assign("modurgence", 0);
$smarty->assign("today"     , $today);
$smarty->assign("tomorow"   , $tomorow);
$smarty->assign("ufs"       , CUniteFonctionnelle::getUFs());

$smarty->assign("categorie_prat", $categorie_prat);
$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("listServices"  , $services);
$smarty->assign("etablissements", $etablissements);

$smarty->assign("hours"        , $hours);
$smarty->assign("mins"         , $mins);
$smarty->assign("hours_duree"  , $hours_duree);
$smarty->assign("hours_urgence", $hours_urgence);
$smarty->assign("mins_duree"   , $mins_duree);

$smarty->assign("list_hours_voulu"  , $list_hours_voulu);
$smarty->assign("list_minutes_voulu", $list_minutes_voulu);

$smarty->assign("prestations", $prestations);
$smarty->assign("count_prestations", $count_prestations);

$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("count_etab_externe"    , $count_etab_externe);
$smarty->assign("listAnesthType"        , $listAnesthType);
$smarty->assign("anesthesistes"         , $anesthesistes);
$smarty->assign("medecin_adresse_par"   , $medecin_adresse_par);
$smarty->assign("blocages_lit"          , $blocages_lit);

$smarty->display("vw_edit_planning.tpl");

?>