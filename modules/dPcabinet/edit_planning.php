<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Recuperation de l'id de la consultation du passage en urgence
$consult_urgence_id = CValue::get("consult_urgence_id");

$consult = new CConsultation();
$chir = new CMediusers;
$pat = new CPatient;
$plageConsult = new CPlageconsult();

//Chargement des aides
$consult->loadAides($AppUI->user_id);

// L'utilisateur est-il praticien?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
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

// Nouvelle consultation
if (!$consultation_id) {
  // A t'on fourni une plage de consultation
  if($plageconsult_id){
    $plageConsult->load($plageconsult_id);    
  } 
  else {
    // A t'on fourni l'id du praticien
    $chir_id = CAppUI::conf("dPcabinet keepchir") ? CValue::getOrSession("chir_id") : CValue::get("chir_id");
    if ($chir_id) {
      $chir->load($chir_id);
    }

    // A t'on fourni l'id du patient
    if ($pat_id = CValue::get("pat_id")) {
      $pat->load($pat_id);
    }
  }
} 

// Consultation existente
else {
  $consult->load($consultation_id);
  $canConsult = $consult->canDo();
  
  $canConsult->needsRead("consultation_id");
  
  $consult->loadRefs();
  $consult->_ref_plageconsult->loadRefs();  
  
  if ($consult->sejour_id) {
    $consult->loadRefSejour();
  }
  
  $chir =& $consult->_ref_plageconsult->_ref_chir;
  $pat  =& $consult->_ref_patient;
}

$categorie = new CConsultationCategorie();
$whereCategorie["function_id"] = " = '$chir->function_id'";
$orderCategorie = "nom_categorie ASC";
$categories = $categorie->loadList($whereCategorie,$orderCategorie);


// Creation du tableau de categories simplifié pour le traitement en JSON
$listCat = array();
foreach($categories as $key => $cat){
  $listCat[$cat->_id] = $cat->nom_icone;
}


// Ajout du motif de la consultation passé en parametre
if(!$consult->_id && $consult_urgence_id){
  // Chargement de la consultation de passage aux urgences
  $consultUrgence = new CConsultation();
  $consultUrgence->load($consult_urgence_id);
  $consultUrgence->loadRefSejour();
  $consultUrgence->_ref_sejour->loadRefRPU();
  $consult->motif = "Reconvocation suite à un passage aux urgences, {$consultUrgence->_ref_sejour->_ref_rpu->motif}";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCat"           , $listCat           );
$smarty->assign("categories"        , $categories        );
$smarty->assign("plageConsult"     	, $plageConsult      );
$smarty->assign("consult"           , $consult           );
$smarty->assign("chir"              , $chir              );
$smarty->assign("pat"               , $pat               );
$smarty->assign("listPraticiens"    , $listPraticiens    );
$smarty->assign("listFunctions"     , $listFunctions     );

$smarty->display("addedit_planning.tpl");

?>