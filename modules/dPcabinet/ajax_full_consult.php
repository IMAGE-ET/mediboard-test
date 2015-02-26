<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

$user = CMediusers::get();

$consult_id        = CValue::getOrSession("consult_id");
$dossier_anesth_id = CValue::getOrSession("dossier_anesth_id");

if (!isset($current_m)) {
  global $m;
  $current_m = CValue::get("current_m", $m);
}

$listPrats = $listChirs = CConsultation::loadPraticiens(PERM_EDIT);

$listAnesths = $user->loadAnesthesistes();

$list_mode_sortie = array();

$consult = new CConsultation();if ($current_m == "dPurgences") {
  if (!$selConsult) {
    CAppUI::setMsg("Vous devez selectionner une consultation", UI_MSG_ALERT);
    CAppUI::redirect("m=urgences&tab=0");
  }
  
  $user = CAppUI::$user;
  $group = CGroups::loadCurrent();
  $listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);
}

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null, $orderBanque);

// Test compliqué afin de savoir quelle consultation charger
if ($consult->load($consult_id) && $consult->patient_id) {
  $consult->loadRefPlageConsult();
}

// On charge le praticien
$userSel = new CMediusers();
$userSel->load($consult->_ref_plageconsult->chir_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// assign to session the current consultation praticien
$chirSession = CValue::session("chirSel");
if ($chirSession != $userSel->_id && $canUserSel) {
  CValue::setSession("chirSel", $userSel->_id);
}

$anesth = new CTypeAnesth();
$anesth = $anesth->loadGroupList();

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
  $patient->countBackRefs("consultations");
  $patient->countBackRefs("sejours");
  $patient->countINS();
  
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
    }
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
}
else {
  $consultAnesth->_id = 0;
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
  $consult->loadRefAdresseParPraticien();
}

// Chargement des boxes 
$services = array();

$sejour = $consult->loadRefSejour();

// Chargement du sejour
if ($consult->_ref_sejour && $sejour->_id) {
  $consult->_ref_sejour->loadRefCurrAffectation();
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->loadNDA();

  // Cas des urgences
  $rpu = $sejour->loadRefRPU();
  if ($rpu->_id) {
    // Mise en session du rpu_id
    $_SESSION["dPurgences"]["rpu_id"] = $rpu->_id;
    $rpu->loadRefSejourMutation();
    $sejour->loadRefCurrAffectation()->loadRefService();

    // Urgences pour un séjour "urg"
    if ($sejour->type == "urg") {
      $services = CService::loadServicesUrgence();
    }

    if ($sejour->_ref_curr_affectation->_ref_service->radiologie == "1") {
      $services = array_merge($services, CService::loadServicesImagerie());
    }
    
    // UHCD pour un séjour "comp" et en UHCD
    if ($sejour->type == "comp" && $sejour->UHCD) {
      $services = CService::loadServicesUHCD();
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

$consult->loadRefGrossesse();

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
$smarty->assign("banques"        , $banques);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listChirs"      , $listChirs);
$smarty->assign("listPrats"      , $listPrats);
$smarty->assign("date"           , $date);
$smarty->assign("userSel"        , $userSel);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("consult"        , $consult);
$smarty->assign("antecedent"     , $antecedent);
$smarty->assign("traitement"     , $traitement);
$smarty->assign("techniquesComp" , $techniquesComp);
$smarty->assign("examComp"       , $examComp);
$smarty->assign("_is_anesth"     , $consult->_is_anesth);
$smarty->assign("_is_dentiste"   , $consult->_is_dentiste);
$smarty->assign("list_etat_dents", $list_etat_dents);
$smarty->assign("tabs_count"     , CConsultation::makeTabsCount($consult, $dossier_medical, $consultAnesth, $sejour, $list_etat_dents));

if (CModule::getActive("dPprescription")) {
  $smarty->assign("line"           , new CPrescriptionLineMedicament());
}

if ($consult->_is_dentiste) {
  $devenirs_dentaires = $consult->_ref_patient->loadRefsDevenirDentaire();
  foreach ($devenirs_dentaires as $_devenir) {
    $etudiant = $_devenir->loadRefEtudiant();
    $etudiant->loadRefFunction();
    $actes_dentaires  = $_devenir->countRefsActesDentaires();
  }
  
  $smarty->assign("devenirs_dentaires", $devenirs_dentaires);
}

if ($consult->_is_anesth) {
  $nextSejourAndOperation = $consult->_ref_patient->getNextSejourAndOperation($consult->_ref_plageconsult->date, true, $consult->_id);
  
  $secs = range(0, 60-1, 1);
  $mins = range(0, 15-1, 1);
  
  $smarty->assign("nextSejourAndOperation", $nextSejourAndOperation);
  $smarty->assign("secs"                  , $secs);
  $smarty->assign("mins"                  , $mins);
  $smarty->assign("consult_anesth"        , $consultAnesth);
  $smarty->display("../../dPcabinet/templates/inc_full_consult.tpl");  
}
else {
  $where = array();
  $where["entree"] = "<= '".CMbDT::dateTime()."'";
  $where["sortie"] = ">= '".CMbDT::dateTime()."'";
  $where["function_id"] = "IS NOT NULL";
  
  $affectation = new CAffectation();
  /** @var CAffectation[] $blocages_lit */
  $blocages_lit = $affectation->loadList($where);
  
  $where["function_id"] = "IS NULL";
  
  foreach ($blocages_lit as $blocage) {
    $blocage->loadRefLit()->loadRefChambre()->loadRefService();
    $where["lit_id"] = "= '$blocage->lit_id'";
    
    if ($affectation->loadObject($where)) {
      $sejour = $affectation->loadRefSejour();
      $patient = $sejour->loadRefPatient();
      $blocage->_ref_lit->_view .= " indisponible jusqu'à ".CMbDT::transform($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y");
      $blocage->_ref_lit->_view .= " (".$patient->_view." (".strtoupper($patient->sexe).") ";
      $blocage->_ref_lit->_view .= CAppUI::conf("dPurgences age_patient_rpu_view") ? $patient->_age.")" : ")" ;
    }
  }
  $smarty->assign("blocages_lit"  , $blocages_lit);
  $smarty->assign("consult_anesth", null);
  
  $smarty->display("../../dPcabinet/templates/inc_full_consult.tpl");
}
