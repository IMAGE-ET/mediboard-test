<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDO::checkRead();

// params
$consult_urgence_id = CValue::get("consult_urgence_id");  // Recuperation de l'id de la consultation du passage en urgence
$dialog             = CValue::get("dialog", 0);
$modal              = CValue::get("modal", 0);
$callback           = CValue::get("callback");
$consultation_id    = CValue::getOrSession("consultation_id");
$plageconsult_id    = CValue::get("plageconsult_id", null);
$line_element_id    = CValue::get("line_element_id");
$sejour_id          = CValue::get("sejour_id");
$date_planning      = CValue::get("date_planning", null);
$heure              = CValue::get("heure", null);
$grossesse_id       = CValue::get("grossesse_id");

$user = CUser::get();

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
$listPraticiens = CConsultation::loadPraticiens(PERM_EDIT);

$function       = new CFunctions();
$listFunctions  = $function->loadSpecialites(PERM_EDIT);

$correspondantsMedicaux = array();
$medecin_adresse_par = "";
$_function_id = null;
$nb_plages = 0;
$count_next_plage = 0;

// On a fourni l'id du praticien
$chir_id = CAppUI::conf("dPcabinet keepchir") ?
  CValue::getOrSession("chir_id") :
  CValue::get("chir_id");

// Nouvelle consultation
if (!$consultation_id) {

  if ($plageconsult_id) {
    // On a fourni une plage de consultation
    $plageConsult->load($plageconsult_id);
  }
  else {
    if ($chir_id) {
      $chir = new CMediusers();
      $chir->load($chir_id);
    }
  }

  // assign patient if defined in get
  if ($pat_id = CValue::get("pat_id")) {
    // On a fourni l'id du patient
    $pat->load($pat_id);
  }
  if ($date_planning) {
    // On a fourni une date
    $consult->_date = $date_planning;
  }
  if ($heure) {
    // On a fourni une heure
    $consult->heure = $heure;
    $consult->plageconsult_id = $plageconsult_id;
    $chir->load($plageConsult->chir_id);
  }

  // grossesse
  if (!$consult->grossesse_id && $grossesse_id) {
    $consult->grossesse_id = $grossesse_id;
  }
  if (CModule::getActive("maternite")) {
    $grossesse = $consult->loadRefGrossesse();
    if (!$consult->patient_id) {
      $consult->patient_id = $grossesse->parturiente_id;
    }
  }

  if ($line_element_id) {
    // RDV issu d'une ligne d'élément
    $consult->sejour_id = $sejour_id;

    $line = new CPrescriptionLineElement();
    $line->load($line_element_id);
    $func_categ = reset($line->_ref_element_prescription->_ref_category_prescription->loadBackRefs("functions_category", null, "1"));
    $plageconsult = new CPlageconsult();

    $where = $ljoin = array();

    $where["pour_tiers"] = "= '1'";
    $where["date"] = "BETWEEN '".CMbDT::date() . "' AND '".CMbDT::date("+3 month") . "'";

    if ($func_categ) {
      $_function_id = $func_categ->function_id;
      $where["users_mediboard.function_id"] = "= '$_function_id'";
      $ljoin["users_mediboard"] = "users_mediboard.user_id = plageconsult.chir_id";
    }
    $nb_plages = $plageconsult->countList($where, null, $ljoin);
  }
}
else {
  // Consultation existante
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
    $consult->loadRefAdresseParPraticien();
  }

  // grossesse
  if (CModule::getActive("maternite")) {
    $consult->loadRefGrossesse();
  }

  $sejour = new CSejour();
  $whereSejour = array();
  $group = CGroups::loadCurrent();
  $whereSejour["type"] = "!= 'consult'";
  $whereSejour[] = "'$consult->_date' BETWEEN DATE(entree) AND DATE(sortie)";
  $whereSejour["patient_id"] = "= '$consult->patient_id'";
  $whereSejour["group_id"] = "= '$group->_id'";
  $consult->_count_matching_sejours = $sejour->countList($whereSejour);

  //next consultation ?
  $dateW = $consult->_ref_plageconsult->date;
  $where = array();
  $whereN["patient_id"] = " = '$consult->patient_id'";
  $whereN["date"] = " >= '$dateW'";
  $ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";
  $count_next_plage = $consult->countList($whereN, null, $ljoin);
}

if (!$modal) {
  // Save history
  $params = array(
    "consult_urgence_id" => $consult_urgence_id,
    "consultation_id"    => $consultation_id,
    "plageconsult_id"    => $plageconsult_id,
    "sejour_id"          => $sejour_id,
    "date_planning"      => $date_planning,
    "grossesse_id"       => $grossesse_id,
  );

  $object = null;
  $type = CViewHistory::TYPE_VIEW;

  if ($consultation_id) {
    $object = $consult;
    $type = CViewHistory::TYPE_EDIT;
  }
  elseif ($plageconsult_id) {
    $object = new CPlageconsult();
    $object->load($plageconsult_id);
    $type = CViewHistory::TYPE_NEW;
  }
  else {
    $object = $chir;
  }

  CViewHistory::save($object, $type, $params);
}

// Chargement des categories
$categorie = new CConsultationCategorie();
$whereCategorie["function_id"] = " = '$chir->function_id'";
$orderCategorie = "nom_categorie ASC";
/** @var CConsultationCategorie[] $categories */
$categories = $categorie->loadList($whereCategorie, $orderCategorie);

// Creation du tableau de categories simplifié pour le traitement en JSON
$listCat = array();
foreach ($categories as $_categorie) {
  $listCat[$_categorie->_id] = array(
    "nom_icone"   => $_categorie->nom_icone,
    "duree"       => $_categorie->duree,
    "commentaire" => utf8_encode($_categorie->commentaire));
}

// Ajout du motif de la consultation passé en parametre
if (!$consult->_id && $consult_urgence_id) {
  // Chargement de la consultation de passage aux urgences
  $consultUrgence = new CConsultation();
  $consultUrgence->load($consult_urgence_id);
  $consultUrgence->loadRefSejour();
  $consultUrgence->_ref_sejour->loadRefRPU();
  $consult->motif = "Reconvocation suite à un passage aux urgences, {$consultUrgence->_ref_sejour->_ref_rpu->motif}";
}

// Locks sur le rendez-vous
$consult->_locks = null;
$today = CMbDT::date();
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

// Consultation suivantes, en cas de suppression ou annulation
$following_consultations = array();
if ($pat->_id) {
  $from_date = CAppUI::pref("today_ref_consult_multiple") ? CMbDT::date() : $consult->_date ;
  $where["date"] = ">= '$from_date'";
  $where["chrono"] = "< '48'";
  $where["annule"] = "= '0'";
  $following_consultations = $pat->loadRefsConsultations($where);
  unset($following_consultations[$consult->_id]);                   //removing the targeted consultation
  foreach ($following_consultations as $_consultation) {
    $_consultation->loadRefPraticien()->loadRefFunction();
    $_consultation->canEdit();
  }
}

// Affichage de l'autocomplete des éléments de prescription
$display_elt = false;

if (CModule::getActive("dPprescription")) {
  $consult->loadRefElementPrescription();

  if ($consult->_id) {
    $task = $consult->loadRefTask();
    if (!$task->_id || !$task->prescription_line_element_id) {
      $display_elt = true;
    }
  }
  else if (!$line_element_id) {
    $elt = new CElementPrescription();
    $elt->consultation = 1;
    if ($elt->countMatchingList()) {
      $display_elt = true;
    }
  }
}

$consult->loadPosition();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCat"                , $listCat);
$smarty->assign("categories"             , $categories);
$smarty->assign("plageConsult"     	     , $plageConsult);
$smarty->assign("consult"                , $consult);
$smarty->assign("following_consultations", $following_consultations);
$smarty->assign("today_ref_multiple"     , CAppUI::pref("today_ref_consult_multiple"));
$smarty->assign("chir"                   , $chir);
$smarty->assign("_functions"             , $_functions);
$smarty->assign("pat"                    , $pat);
$smarty->assign("listPraticiens"         , $listPraticiens);
$smarty->assign("listFunctions"          , $listFunctions);
$smarty->assign("correspondantsMedicaux" , $correspondantsMedicaux);
$smarty->assign("medecin_adresse_par"    , $medecin_adresse_par);
$smarty->assign("today"                  , $today);
$smarty->assign("date_planning"          , $date_planning);
$smarty->assign("_function_id"           , $_function_id);
$smarty->assign("line_element_id"        , $line_element_id);
$smarty->assign("nb_plages"              , $nb_plages);
$smarty->assign("dialog"                 , $dialog);
$smarty->assign("modal"                  , $modal);
$smarty->assign("callback"               , $callback);
$smarty->assign("next_consult"           , $count_next_plage);
$smarty->assign("display_elt"            , $display_elt);
$smarty->assign("ufs"                    , CUniteFonctionnelle::getUFs());

$smarty->display("edit_planning.tpl");
