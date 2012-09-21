<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDO::checkRead();

$user = CUser::get();

// Recuperation de l'id de la consultation du passage en urgence
$consult_urgence_id = CValue::get("consult_urgence_id");
$dialog = CValue::get("dialog", 0);

$consult      = new CConsultation();
$chir         = new CMediusers();
$pat          = new CPatient();
$plageConsult = new CPlageconsult();

// L'utilisateur est-il praticien?
$mediuser = CMediusers::get();
if ($mediuser->isMedical()) {
  $chir = $mediuser;
}

// Vérification des droits sur les praticiens et les fonctions
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);
} else {
  $listPraticiens = $mediuser->loadProfessionnelDeSante(PERM_EDIT);
}

$function       = new CFunctions();
$listFunctions  = $function->loadSpecialites(PERM_EDIT);

$consultation_id = CValue::getOrSession("consultation_id");
$plageconsult_id = CValue::get("plageconsult_id", null);

$date_planning   = CValue::get("date_planning", null);
$heure           = CValue::get("heure", null);

$correspondantsMedicaux = array();
$medecin_adresse_par = "";

// Nouvelle consultation
if (!$consultation_id) {

  // A t'on fourni une plage de consultation
  if ($plageconsult_id){
    $plageConsult->load($plageconsult_id);
  } 
  // A t'on fourni l'id du praticien
  else {
    $chir_id = CAppUI::conf("dPcabinet keepchir") ?
      CValue::getOrSession("chir_id") : 
      CValue::get("chir_id");
    
    if ($chir_id) {
      $chir = new CMediusers();
      $chir->load($chir_id);
    }
  }

  // A t'on fourni l'id du patient
  if ($pat_id = CValue::get("pat_id")) {
    $pat->load($pat_id);
  }
  
  // A t'on fourni une date
  if ($date_planning) {
    $consult->_date = $date_planning;
  }
  
  // A t'on fourni une heure
  if ($heure) {
    $consult->heure = $heure;
    $consult->plageconsult_id = $plageconsult_id;
    $chir->load($plageConsult->chir_id);
  }
} 

// Consultation existante
else {
  $consult->load($consultation_id);
  $canConsult = $consult->canDo();
  
  $canConsult->needsRead("consultation_id");
  
  $consult->loadRefConsultAnesth();
  $consult->loadRefsNotes();
  $consult->loadRefSejour();
  $consult->loadRefPlageConsult()->loadRefs();  

  $chir = $consult->loadRefPraticien();
    
  $pat = $consult->loadRefPatient();
  $pat->loadIdVitale();
  
  // Correspondants médicaux
  $correspondants = $pat->loadRefsCorrespondants();
  foreach ($correspondants as $_correspondant) {
    $correspondantsMedicaux["correspondants"][] = $_correspondant->_ref_medecin;
  }
  
  if ($pat->_ref_medecin_traitant->_id) {
    $correspondantsMedicaux["traitant"] = $pat->_ref_medecin_traitant;
  }
  
  if ($consult->adresse_par_prat_id && ($consult->adresse_par_prat_id != $pat->_ref_medecin_traitant->_id)) {
    $medecin_adresse_par = new CMedecin();
    $medecin_adresse_par->load($consult->adresse_par_prat_id);
    $consult->_ref_adresse_par_prat = $medecin_adresse_par;
  }
}

// Chargement des categories
$categorie = new CConsultationCategorie();
$whereCategorie["function_id"] = " = '$chir->function_id'";
$orderCategorie = "nom_categorie ASC";
$categories = $categorie->loadList($whereCategorie,$orderCategorie);

// Creation du tableau de categories simplifié pour le traitement en JSON
$listCat = array();
foreach($categories as $cat){
  $listCat[$cat->_id] = array(
    "nom_icone"   => $cat->nom_icone,
    "duree"       => $cat->duree,
    "commentaire" => utf8_encode($cat->commentaire));
}

// Ajout du motif de la consultation passé en parametre
if (!$consult->_id && $consult_urgence_id){
  // Chargement de la consultation de passage aux urgences
  $consultUrgence = new CConsultation();
  $consultUrgence->load($consult_urgence_id);
  $consultUrgence->loadRefSejour();
  $consultUrgence->_ref_sejour->loadRefRPU();
  $consult->motif = "Reconvocation suite à un passage aux urgences, {$consultUrgence->_ref_sejour->_ref_rpu->motif}";
}

// Locks sur le rendez-vous
$consult->_locks = null;
$today = mbDate();
if ($consult->_id) {
  if ($consult->_datetime < $today) {
    $consult->_locks[] = "datetime";
  }
  
  if ($consult->chrono == CConsultation::TERMINE && !$consult->annule) {
    $consult->_locks[] = "termine";
  }
  
  if ($consult->valide) {
    $consult->_locks[] = "valide";
  }
}
$_functions = array();

if ($chir->_id) {
  $chir->loadRefFunction();
  $_functions = $chir->loadBackRefs("secondary_functions");
}

if (CModule::getActive("maternite")) {
  $consult->loadRefGrossesse();
}

// Consultation suivantes, en cas de suppression ou annulation
$following_consultations = array();
if ($pat->_id) {
  $where["date"] = ">= '$consult->_date'";
  $following_consultations = $pat->loadRefsConsultations($where);
  unset($following_consultations[$consult->_id]);
  foreach ($following_consultations as $_consultation) {
    $_consultation->loadRefPlageConsult();
    $_consultation->loadRefPraticien();
    $_consultation->canEdit();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCat"                , $listCat);
$smarty->assign("categories"             , $categories);
$smarty->assign("plageConsult"     	     , $plageConsult);
$smarty->assign("consult"                , $consult);
$smarty->assign("following_consultations", $following_consultations);
$smarty->assign("chir"                   , $chir);
$smarty->assign("_functions"             , $_functions);
$smarty->assign("pat"                    , $pat);
$smarty->assign("listPraticiens"         , $listPraticiens);
$smarty->assign("listFunctions"          , $listFunctions);
$smarty->assign("correspondantsMedicaux" , $correspondantsMedicaux);
$smarty->assign("medecin_adresse_par"    , $medecin_adresse_par);
$smarty->assign("today"                  , $today);
$smarty->assign("date_planning"          , $date_planning);
$smarty->assign("dialog"                 , $dialog);

$smarty->display("addedit_planning.tpl");
