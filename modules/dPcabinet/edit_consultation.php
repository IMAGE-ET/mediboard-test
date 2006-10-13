<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $dPconfig;

if (!$canEdit) {
	$AppUI->redirect("m=system&a=access_denied");
}

$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;

$date         = mbGetValueFromGetOrSession("date", mbDate());
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);
$today        = mbDate();
$hour         = mbTime(null);
$_is_anesth   = false;
$urlDHEParams = array();

$prat_id      = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$selConsult   = mbGetValueFromGetOrSession("selConsult", null);


$etablissements = CMediusers::loadEtablissements(PERM_EDIT);
$consult = new CConsultation();

if(isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", null);
}

// Test compliqué afin de savoir quelle consultation charger
if(isset($_GET["selConsult"])) {
  if($consult->load($selConsult)) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    mbSetValueToSession("chirSel", $prat_id);
  } else {
    $selConsult = null;
    mbSetValueToSession("selConsult");
  }
} else {
  if($consult->load($selConsult)) {
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

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->canEdit()) {
  mbSetValueToSession("chirSel", 0);
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

// Consultation courante
$consult->_ref_chir =& $userSel;
if($consult->consultation_id) {
  $consult->loadRefs();
  $consult->loadAides($userSel->user_id);
  $consult->_ref_consult_anesth->loadAides($userSel->user_id);
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadStaticCIM10($userSel->user_id);
  foreach($patient->_ref_consultations as $key => $curr_cons) {
    $patient->_ref_consultations[$key]->loadRefsFwd();
  }
  foreach($patient->_ref_sejours as $key => $curr_sejour) {
    $patient->_ref_sejours[$key]->loadRefsFwd();
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
  $codePraticienEc = null;
  $urlDHE = "#";
  if(CModule::getInstalled("dPsante400") && ($dPconfig["interop"]["mode_compat"] == "medicap")) {
    $tmpEtab = array();
    foreach($etablissements as $etab) {
      $idExt = new CIdSante400;
      $idExt->loadLatestFor($etab);
      if($idExt->id400) {
        $tmpEtab[$idExt->id400] = $etab;
      }
    }
    $etablissements = $tmpEtab;
    
    // Construction de l'URL
    $urlDHE = $dPconfig["interop"]["base_url"];
    $urlDHEParams["logineCap"]       = "";
    $urlDHEParams["codeAppliExt"]    = "mediboard";
    $urlDHEParams["patIdentLogExt"]  = $patient->patient_id;
    $urlDHEParams["patNom"]          = $patient->nom;
    $urlDHEParams["patPrenom"]       = $patient->prenom;
    $urlDHEParams["patNomJF"]        = $patient->nom_jeune_fille;
    $urlDHEParams["patSexe"]         = $patient->sexe == "m" ? "1" : "2";
    $urlDHEParams["patDateNaiss"]    = $patient->_naissance;
    $urlDHEParams["patAd1"]          = $patient->adresse;
    $urlDHEParams["patCP"]           = $patient->cp;
    $urlDHEParams["patVille"]        = $patient->ville;
    $urlDHEParams["patTel1"]         = $patient->tel;
    $urlDHEParams["patTel2"]         = $patient->tel2;
    $urlDHEParams["patTel3"]         = "";
    $urlDHEParams["patNumSecu"]      = substr($patient->matricule, 0, 13);
    $urlDHEParams["patCleNumSecu"]   = substr($patient->matricule, 13, 2);
    $urlDHEParams["interDatePrevue"] = "";

    $idExt = new CIdSante400;
    $idExt->loadLatestFor($patient);
    $patIdentEc = $idExt->id400;
    $urlDHEParams["patIdentEc"]      = $patIdentEc;

    $idExt = new CIdSante400;
    $idExt->loadLatestFor($userSel);
    $codePraticienEc = $idExt->id400;
    $urlDHEParams["codePraticienEc"] = $codePraticienEc;
  }
} else {
  $consult->_ref_consult_anesth->consultation_anesth_id = 0;
}

// Récupération des modèles
$whereCommon = array();
if($consult->_ref_consult_anesth->consultation_anesth_id){
  $catCptRendu = "Consultation Anesthésique";
}else{
  $catCptRendu = "Consultation";
}

// Modèles de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
	$where = array();
  $where["chir_id"] = db_prepare("= %", $userSel->user_id);
  $listModelePrat = CCompteRendu::loadModeleByCat($catCptRendu, $where);
}

// Modèles de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
	$where = array();
	$where["function_id"] = db_prepare("= %", $userSel->function_id);
  $listModeleFunc = CCompteRendu::loadModeleByCat($catCptRendu, $where);
}

// Récupération des tarifs
$order = "description";
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$tarifsChir = new CTarif;
$tarifsChir = $tarifsChir->loadList($where, $order);
$where = array();
$where["function_id"] = "= '$userSel->function_id'";
$tarifsCab = new CTarif;
$tarifsCab = $tarifsCab->loadList($where, $order);

// Chargement des aides à la saisie
$antecedent = new CAntecedent();
$antecedent->loadAides($userSel->user_id);

$traitement = new CTraitement();
$traitement->loadAides($userSel->user_id);

$techniquesComp = new CTechniqueComp();
$techniquesComp->loadAides($userSel->user_id);

$examComp = new CExamComp();
$examComp->loadAides($userSel->user_id);


// Classement des antecedents
$listAnt = array();
foreach($antecedent->_enumsTrans["type"] as $keyAnt => $currAnt){
  $listAnt[$keyAnt] = array();
}
if($consult->consultation_id) {
  foreach($patient->_ref_antecedents as $keyAnt => $currAnt){
    $listAnt[$currAnt->type][$keyAnt] = $currAnt;
  }
}

// Vérification du cas anesthésie
if($consult->_ref_chir->isFromType(array("Anesthésiste"))) {
  $_is_anesth=true; 
} else {
  $_is_anesth=false;
}
// Création du template
$smarty = new CSmartyDP(1);
$smarty->assign("codePraticienEc", $codePraticienEc);
$smarty->assign("urlDHE"         , $urlDHE);
$smarty->assign("urlDHEParams"   , $urlDHEParams);
$smarty->assign("etablissements" , $etablissements);
$smarty->assign("listAnt"        , $listAnt);
$smarty->assign("date"           , $date);
$smarty->assign("hour"           , $hour);
$smarty->assign("vue"            , $vue);
$smarty->assign("today"          , $today);
$smarty->assign("userSel"        , $userSel);
$smarty->assign("listModelePrat" , $listModelePrat);
$smarty->assign("listModeleFunc" , $listModeleFunc);
$smarty->assign("tarifsChir"     , $tarifsChir);
$smarty->assign("tarifsCab"      , $tarifsCab);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("consult"        , $consult);
$smarty->assign("antecedent"     , $antecedent);
$smarty->assign("traitement"     , $traitement);
$smarty->assign("techniquesComp" , $techniquesComp);
$smarty->assign("examComp"       , $examComp);
$smarty->assign("_is_anesth"     , $_is_anesth);  

if($_is_anesth) {
  $secs = array();
  for ($i = 0; $i < 60; $i++) {
    $secs[] = $i;
  }
  $mins = array();
  for ($i = 0; $i < 15; $i++) {
    $mins[] = $i;
  }
  
  $smarty->assign("secs"          , $secs);
  $smarty->assign("mins"          , $mins);
  $smarty->assign("consult_anesth", $consult->_ref_consult_anesth);
  $smarty->display("edit_consultation_anesth.tpl");
} else {
	$vue_accord = isset($AppUI->user_prefs["MODCONSULT"]) ? $AppUI->user_prefs["MODCONSULT"] : 0 ;
  if($vue_accord){
    $smarty->display("edit_consultation_accord.tpl");
  }else{  
    $smarty->display("edit_consultation_classique.tpl");
  }
}
?>