<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/


global $AppUI, $can, $m, $g, $dPconfig;

$can->needsEdit();
$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;

$date         = mbGetValueFromGetOrSession("date", mbDate());
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);
$today        = mbDate();
$hour         = mbTime(null);

$now = mbDateTime();

if(!isset($current_m)){
  $current_m = mbGetValueFromGet("current_m", $m);
}

$prat_id      = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$selConsult   = mbGetValueFromGetOrSession("selConsult", null);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(null);

$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$consult = new CConsultation();

if($current_m == "dPurgences"){
  if (!$selConsult) {
    $AppUI->setMsg("Vous devez selectionner une consultation", UI_MSG_ALERT);
    $AppUI->redirect("m=dPurgences&tab=0");
  }
}

$tabSejour = array();

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null,$orderBanque);

if(isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", null);
}

// Test compliqué afin de savoir quelle consultation charger
if (isset($_GET["selConsult"])) {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    mbSetValueToSession("chirSel", $prat_id);
  } else {
    $consult = new CConsultation();
    $selConsult = null;
    mbSetValueToSession("selConsult");
  }
} else {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    if($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      mbSetValueToSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if ((!$userSel->isPraticien()) && ($current_m != "dPurgences")) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit(array("chirSel"=>0));

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

$consultAnesth =& $consult->_ref_consult_anesth;

// Consultation courante
$consult->_ref_chir =& $userSel;

// Chargement de la consultation
if ($consult->_id) {
  $consult->loadRefs();  
  $consult->loadAides($userSel->user_id);
  
  // Chargment de la consultation d'anesthésie
  $consultAnesth->loadAides($userSel->user_id);
  if($consultAnesth->consultation_anesth_id) {
    $consultAnesth->loadRefs();
    if($consultAnesth->_ref_operation->_id || $consultAnesth->_ref_sejour->_id) {
    	$consultAnesth->_ref_operation->loadExtCodesCCAM();
      $consultAnesth->_ref_operation->loadAides($userSel->user_id);
      $consultAnesth->_ref_operation->loadRefs();
      $consultAnesth->_ref_sejour->loadRefPraticien();
    }
  }
  
  // Chargement du patient
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadRefPhotoIdentite();
  $patient->loadStaticCIM10($userSel->user_id);
  
  // Chargement des ses consultations
  foreach ($patient->_ref_consultations as &$_consultation) {
    $_consultation->loadRefsFwd();
  }
  
  // Chargement des ses séjours
  foreach ($patient->_ref_sejours as &$_sejour) {
    $_sejour->loadRefsFwd();
    $_sejour->loadRefsOperations();
    foreach ($_sejour->_ref_operations as &$_operation) {
      $_operation->loadRefsFwd();
      // Tableaux de correspondances operation_id => sejour_id
      $tabSejour[$_operation->_id] = $_sejour->_id;
    }
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
} else {
  $consultAnesth->consultation_anesth_id = 0;
}

if ($consult->_id){
  // Chargement des identifiants LogicMax
  $consult->loadIdsFSE();
  $consult->makeFSE();
  $consult->_ref_chir->loadIdCPS();
  $consult->_ref_patient->loadIdVitale();
}


// Chargement des aides à la saisie
$antecedent = new CAntecedent();
//$antecedent->loadAides($userSel->user_id);

$traitement = new CTraitement();
$traitement->loadAides($userSel->user_id);

$techniquesComp = new CTechniqueComp();
$techniquesComp->loadAides($userSel->user_id);

$examComp = new CExamComp();
$examComp->loadAides($userSel->user_id);

$consult->loadExtCodesCCAM();
$consult->getAssociationCodesActes();
$consult->loadPossibleActes();

// Chargement du dossier medical du patient de la consultation
if ($consult->patient_id) {
  $consult->_ref_patient->loadRefDossierMedical();
  $consult->_ref_patient->_ref_dossier_medical->updateFormFields();
}

// Chargement des actes NGAP
$consult->loadRefsActesNGAP();

$listEtab = array();
$listServicesUrgence = array();

// Chargement du sejour dans le cas d'une urgence
if ($consult->_id && $consult->sejour_id){
  $consult->loadRefSejour();
  $consult->_ref_sejour->loadExtDiagnostics();
  $consult->_ref_sejour->loadRefDossierMedical();
  $consult->_ref_sejour->loadNumDossier();
  $consult->_ref_sejour->_ref_rpu->loadAides($AppUI->user_id);
  $consult->_ref_sejour->_ref_rpu->loadRefSejourMutation();

  // Chargement des etablissements externes
  $order = "nom";
  $etab = new CEtabExterne();
  $listEtab = $etab->loadList(null, $order);
  
  // Chargement des boxes d'urgences
  $listServicesUrgence = CService::loadServicesUrgence();
}

// Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite = 1;
$acte_ngap->coefficient = 1;
$acte_ngap->loadListExecutants();


// Tableau de contraintes pour les champs du RPU
// Contraintes sur le mode d'entree / provenance
//$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Contraintes sur le mode de sortie / destination
//$contrainteDestination[6] = array("", 1, 2, 3, 4);
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"] = array("", "FUGUE", "SCAM", "PSA", "REO");

$list_etat_dents = array();
if ($consult->_id) {
	if ($consult->_ref_patient->_ref_dossier_medical->_id) {
  	$consult->_ref_patient->_ref_dossier_medical->loadRefsEtatsDents();
  	$etat_dents = $consult->_ref_patient->_ref_dossier_medical->_ref_etats_dents;
  	foreach ($etat_dents as $etat) {
  	  $list_etat_dents[$etat->dent] = $etat->etat;
  	}
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("contrainteProvenance" , $contrainteProvenance );
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);

$smarty->assign("listEtab"           , $listEtab           );
$smarty->assign("listServicesUrgence", $listServicesUrgence);

$smarty->assign("acte_ngap"      , $acte_ngap);
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
$smarty->assign("current_m"      , $current_m);
$smarty->assign("list_etat_dents", $list_etat_dents);
$smarty->assign("line", new CPrescriptionLineMedicament());
$smarty->assign("now", $now);

if($consult->_is_anesth) {
  $nextSejourAndOperation = $consult->_ref_patient->getNextSejourAndOperation($consult->_ref_plageconsult->date);
	$secs = range(0, 60-1, 1);
	$mins = range(0, 15-1, 1);
	
	$smarty->assign("nextSejourAndOperation", $nextSejourAndOperation);
	$smarty->assign("secs"                  , $secs);
	$smarty->assign("mins"                  , $mins);
	$smarty->assign("consult_anesth"        , $consultAnesth);
	$smarty->display("../../dPcabinet/templates/edit_consultation_anesth.tpl");  
} else {
    $vue_accord = isset($AppUI->user_prefs["MODCONSULT"]) ? $AppUI->user_prefs["MODCONSULT"] : 0 ;
  if($vue_accord){
    $smarty->display("../../dPcabinet/templates/edit_consultation_accord.tpl");
  } else{  
    $smarty->display("../../dPcabinet/templates/edit_consultation_classique.tpl");
  }
}
?>