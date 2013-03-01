<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     Romain Ollivier <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

global $m;

$user = CMediusers::get();

$date  = CValue::getOrSession("date", mbDate());
$vue   = CValue::getOrSession("vue2", CAppUI::pref("AFFCONSULT", 0));
$today = mbDate();
$hour  = mbTime(null);
$now   = mbDateTime();

CMbObject::$useObjectCache = false;

if (!isset($current_m)) {
  $current_m = CValue::get("current_m", $m);
}

$prat_id      = CValue::getOrSession("chirSel", $user->_id);
$selConsult   = CValue::getOrSession("selConsult");
$dossier_anesth_id = CValue::getOrSession("dossier_anesth_id");

$listChirs = CAppUI::pref("pratOnlyForConsult", 1) ?
  $user->loadPraticiens(null) :
  $user->loadProfessionnelDeSante(null);

$listAnesths = $user->loadAnesthesistes();

$listPrats = array();

$list_mode_sortie = array();

$consult = new CConsultation();
if ($current_m == "dPurgences") {
  if (!$selConsult) {
    CAppUI::setMsg("Vous devez selectionner une consultation", UI_MSG_ALERT);
    CAppUI::redirect("m=urgences&tab=0");
  }
  
  $user = CAppUI::$user;
  $group = CGroups::loadCurrent();
  $listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);
}

$tabSejour = array();

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null, $orderBanque);

if (isset($_GET["date"])) {
  $selConsult = null;
  CValue::setSession("selConsult", null);
}

// Test compliqué afin de savoir quelle consultation charger
if (isset($_GET["selConsult"])) {
  if ($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefPlageConsult();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    CValue::setSession("chirSel", $prat_id);
  }
  else {
    $consult = new CConsultation();
    $selConsult = null;
    CValue::setSession("selConsult");
  }
}
else {
  if ($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefPlageConsult();
    if ($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      CValue::setSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

if ((!$userSel->isMedical()) && ($current_m != "dPurgences")) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de santé", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null, $orderanesth);

$consultAnesth =& $consult->_ref_consult_anesth;

// Consultation courante
$consult->_ref_chir =& $userSel;

// Chargement de la consultation
if ($consult->_id) {
  $consult->loadRefs();  
  
  // Chargement de la consultation d'anesthésie
  
  // Chargement de la vue de chacun des dossiers
  foreach ($consult->_refs_dossiers_anesth as $_dossier) {
    $_dossier->loadRefConsultation();
    $_dossier->loadRefOperation()->loadRefPlageOp();
  }
  
  // Si on a passé un id de dossier d'anesth
  if ($dossier_anesth_id && isset($consult->_refs_dossiers_anesth[$dossier_anesth_id])) {
    $consultAnesth = $consult->_refs_dossiers_anesth[$dossier_anesth_id];
  }
  
  if (!is_array($consultAnesth) && $consultAnesth->_id) {
    $consultAnesth->loadRefs();
    if ($consultAnesth->_ref_operation->_id || $consultAnesth->_ref_sejour->_id) {
      if ($consultAnesth->_ref_operation->passage_uscpo === null) {
        $consultAnesth->_ref_operation->passage_uscpo = "";
      }
      $consultAnesth->_ref_operation->loadExtCodesCCAM();
      $consultAnesth->_ref_operation->loadRefs();
      $consultAnesth->_ref_sejour->loadRefPraticien();
    }
  }
 
  // Chargement du patient
  $patient = $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadRefsNotes();  
  $patient->loadRefPhotoIdentite();
  
  // Chargement de ses consultations
  foreach ($patient->_ref_consultations as $_consultation) {
    $_consultation->loadRefsFwd();
    $_consultation->_ref_chir->loadRefFunction()->loadRefGroup();
  }
  
  // Chargement de ses séjours
  foreach ($patient->_ref_sejours as $_sejour) {
    $_sejour->loadRefsFwd();
    $_sejour->loadRefsOperations();
    foreach ($_sejour->_ref_operations as $_operation) {
      $_operation->loadRefsFwd();
      $_operation->_ref_chir->loadRefFunction()->loadRefGroup();
      // Tableaux de correspondances operation_id => sejour_id
      $tabSejour[$_operation->_id] = $_sejour->_id;
    }
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
}
else {
  $consultAnesth->consultation_anesth_id = 0;
}

if ($consult->_id) {
  $consult->canDo();
}

if ($consult->_id && CModule::getActive("fse")) {
  // Chargement des identifiants LogicMax
  $fse = CFseFactory::createFSE();
  if ($fse) {
    $fse->loadIdsFSE($consult);
    $fse->makeFSE($consult);
    
    $cps = CFseFactory::createCPS()->loadIdCPS($consult->_ref_chir);
    
    CFseFactory::createCV()->loadIdVitale($consult->_ref_patient);
  }
}

$antecedent = new CAntecedent();
$traitement = new CTraitement();
$techniquesComp = new CTechniqueComp();
$examComp = new CExamComp();

$consult->loadExtCodesCCAM();
$consult->getAssociationCodesActes();
$consult->loadPossibleActes();
$consult->_ref_chir->loadRefFunction();

// Chargement du dossier medical du patient de la consultation
if ($consult->patient_id) {
  $consult->_ref_patient->loadRefDossierMedical();
  $consult->_ref_patient->_ref_dossier_medical->updateFormFields();
}

// Chargement des actes NGAP
$consult->loadRefsActesNGAP();

// Chargement du medecin adressé par
if ($consult->adresse_par_prat_id) {
  $medecin_adresse_par = new CMedecin();
  $medecin_adresse_par->load($consult->adresse_par_prat_id);
  $consult->_ref_adresse_par_prat = $medecin_adresse_par;
}

// Chargement des boxes 
$services = array();

if ($consult->sejour_id) {
  $sejour = $consult->loadRefSejour();
}

// Chargement du sejour
if ($consult->_ref_sejour && $sejour->_id) {
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->loadNDA();

  // Cas des urgences
  $rpu = $sejour->loadRefRPU();
  if ($rpu->_id) {
    // Mise en session du rpu_id
    $_SESSION["dPurgences"]["rpu_id"] = $rpu->_id;
    $rpu->loadRefSejourMutation();

    // Urgences pour un séjour "urg"
    if ($sejour->type == "urg") {
      $services = CService::loadServicesUrgence();
    }
    
    // UHCD pour un séjour "comp" et en UHCD
    if ($sejour->type == "comp" && $sejour->UHCD) {
      $services = CService::loadServicesUHCD();
    }

    if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
      $mode_sortie = new CModeSortieSejour();
      $where = array(
        "actif" => "= '1'",
      );
      $list_mode_sortie = $mode_sortie->loadGroupList($where);
    }
  }
}

// Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($consult);

// Tableau de contraintes pour les champs du RPU
// Contraintes sur le mode d'entree / provenance
//$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Contraintes sur le mode de sortie / destination
$contrainteDestination["mutation" ] = array("", 1, 2, 3, 4);
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"   ] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["mutation" ] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"   ] = array("", "FUGUE", "SCAM", "PSA", "REO");

$list_etat_dents = array();
if ($consult->_id) {
  $dossier_medical = $consult->_ref_patient->_ref_dossier_medical;
  if ($dossier_medical->_id) {
    $etat_dents = $dossier_medical->loadRefsEtatsDents();
    foreach ($etat_dents as $etat) {
      $list_etat_dents[$etat->dent] = $etat->etat;
    }
  }
}

// Si le module Tarmed est installé chargement d'un acte
$acte_tarmed = null;
$acte_caisse = null;
if (CModule::getActive("tarmed")) {
  $acte_tarmed = CActeTarmed::createEmptyFor($consult);
  $acte_caisse = CActeCaisse::createEmptyFor($consult);
}
$total_tarmed = $consult->loadRefsActesTarmed();
$total_caisse = $consult->loadRefsActesCaisse();
$soustotal_base = array("tarmed" => $total_tarmed["base"], "caisse" => $total_caisse["base"]);
$soustotal_dh   = array("tarmed" => $total_tarmed["dh"], "caisse" => $total_caisse["dh"]);
$total["tarmed"] = round($total_tarmed["base"]+$total_tarmed["dh"],2);
$total["caisse"] = round($total_caisse["base"]+$total_caisse["dh"],2);

if (CModule::getActive("maternite")) {
  $consult->loadRefGrossesse();
}

// Tout utilisateur peut consulter en lecture seule une consultation de séjour
$consult->canEdit();

if ($consult->_ref_patient->_vip) {
  CCanDo::redirect();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("contrainteProvenance" , $contrainteProvenance );
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);

$smarty->assign("services"        , $services);

$smarty->assign("acte_ngap"      , $acte_ngap);
$smarty->assign("acte_tarmed"    , $acte_tarmed);
$smarty->assign("acte_caisse"    , $acte_caisse);
$smarty->assign("tabSejour"      , $tabSejour);
$smarty->assign("banques"        , $banques);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listChirs"      , $listChirs);
$smarty->assign("date"           , $date);
$smarty->assign("hour"           , $hour);
$smarty->assign("vue"            , $vue);
$smarty->assign("today"          , $today);
$smarty->assign("userSel"        , $userSel);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("consult"        , $consult);
$smarty->assign("antecedent"     , $antecedent);
$smarty->assign("traitement"     , $traitement);
$smarty->assign("techniquesComp" , $techniquesComp);
$smarty->assign("examComp"       , $examComp);
$smarty->assign("_is_anesth"     , $consult->_is_anesth);
$smarty->assign("consult_anesth" , $consultAnesth);
$smarty->assign("_is_dentiste"   , $consult->_is_dentiste);
$smarty->assign("current_m"      , $current_m);
$smarty->assign("list_etat_dents", $list_etat_dents);
$smarty->assign("now"            , $now);
$smarty->assign("listPrats"      , $listPrats);
$smarty->assign("list_mode_sortie", $list_mode_sortie);

if (CModule::getActive("dPprescription")) {
  $smarty->assign("line"           , new CPrescriptionLineMedicament());
}

$smarty->assign("soustotal_base" , $soustotal_base);
$smarty->assign("soustotal_dh"   , $soustotal_dh);
$smarty->assign("total"          , $total);

if ($consult->_is_dentiste) {
  $devenirs_dentaires = $consult->_ref_patient->loadRefsDevenirDentaire();
  
  foreach ($devenirs_dentaires as &$devenir_dentaire) {
    $etudiant = $devenir_dentaire->loadRefEtudiant();
    $etudiant->loadRefFunction();
    $actes_dentaires  = $devenir_dentaire->countRefsActesDentaires();
  }
  
  $smarty->assign("devenirs_dentaires", $devenirs_dentaires);
}

if ($consult->_refs_dossiers_anesth|@count) {
  $nextSejourAndOperation = $consult->_ref_patient->getNextSejourAndOperation($consult->_ref_plageconsult->date);
  
  $secs = range(0, 60-1, 1);
  $mins = range(0, 15-1, 1);
  
  $smarty->assign("nextSejourAndOperation", $nextSejourAndOperation);
  $smarty->assign("secs"                  , $secs);
  $smarty->assign("mins"                  , $mins);
  $smarty->display("../../dPcabinet/templates/edit_consultation_anesth.tpl");  
}
else {
  if (CAppUI::pref("MODCONSULT")) {
    $where = array();
    $where["entree"] = "<= '".mbDateTime()."'";
    $where["sortie"] = ">= '".mbDateTime()."'";
    $where["function_id"] = "IS NOT NULL";
    
    $affectation = new CAffectation();
    $blocages_lit = $affectation->loadList($where);
    
    $where["function_id"] = "IS NULL";
    
    foreach ($blocages_lit as $blocage) {
      $blocage->loadRefLit()->loadRefChambre()->loadRefService();
      $where["lit_id"] = "= '$blocage->lit_id'";
      
      if ($affectation->loadObject($where)) {
        $affectation->loadRefSejour();
        $affectation->_ref_sejour->loadRefPatient();
        $blocage->_ref_lit->_view .= " indisponible jusqu'à ".mbTransformTime($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y")." (".$affectation->_ref_sejour->_ref_patient->_view.")";
      }
    }
    $smarty->assign("blocages_lit" , $blocages_lit);
    $smarty->assign("consult_anesth", null);
    
    $smarty->display("../../dPcabinet/templates/edit_consultation_accord.tpl");
  }
  else {
    $smarty->display("../../dPcabinet/templates/edit_consultation_classique.tpl");
  }
}
