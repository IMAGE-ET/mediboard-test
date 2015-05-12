<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$service_id       = CValue::get("service_id");
$praticien_id     = CValue::get("praticien_id");
$function_id      = CValue::get("function_id");
$sejour_id        = CValue::get("sejour_id");
$show_affectation = CValue::get("show_affectation", false);
$only_non_checked = CValue::get("only_non_checked", 0);
$print            = CValue::get("print", false);
$_type_admission  = CValue::getOrSession("_type_admission", "");
$select_view      = CValue::get("select_view", false);
$refresh          = CValue::get('refresh', false);
$ecap             = CValue::get('ecap', false);
$date             = CValue::get('date', CMbDT::date());
$mode             = CValue::get('mode', 'day');
$lite_view        = CValue::get("lite_view");
$my_patient       = CValue::getOrSession("my_patient");

// Mode Dossier de soins, chargement de la liste des service, praticiens, functions
$services = array();
$functions = array();
$praticiens = array();
$dossiers = array();
$group_id = CGroups::loadCurrent()->_id;

if ($select_view || (!$service_id && !$praticien_id && !$function_id && !$sejour_id)) {
  // Redirection pour gérer le cas ou le volet par defaut est l'autre affichage des sejours
  if (CAppUI::pref("vue_sejours") == "standard") {
    CAppUI::redirect("m=soins&tab=vw_idx_sejour");
  }

  // Récupération d'un éventuel service_id en session
  $service_id = CValue::getOrSession("service_id");
  $default_service_id = null;
  $default_services = json_decode(CAppUI::pref("default_services_id"));
  if (isset($default_services->{"g$group_id"})) {
    $default_service_id = reset(explode("|", $default_services->{"g$group_id"}));
  }

  // Récupération d'un éventuel praticien_id ou function_id en session
  if (!$service_id) {
    $praticien_id = CValue::getOrSession("praticien_id");
    $function_id  = CValue::getOrSession("function_id");
  }

  if (!$service_id && $default_service_id && !$praticien_id && !$function_id) {
    $service_id = $default_service_id;
  }

  $select_view = true;
}

// Chargement de l'utilisateur courant
$userCourant = CMediusers::get();
// Preselection du praticien_id
if (!$praticien_id && $userCourant->isPraticien() && !$service_id && !$praticien_id && !$function_id) {
  $praticien_id = $userCourant->user_id;
}

$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadListWithPerms(PERM_READ, $where);

$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

$function = new CFunctions();
$functions = $function->loadSpecialites();

$date_max = CMbDT::date("+ 1 DAY", $date);
$service  = new CService();
$user_id  = CAppUI::$user->_id;

if ($service_id) {
  CValue::setSession("service_id"  , $service_id);
}
if ($praticien_id) {
  CValue::setSession("praticien_id", $praticien_id);
}
if ($function_id) {
  CValue::setSession("function_id" , $function_id);
}

if ($sejour_id) {
  $sejour = new CSejour();
  $sejour->load($sejour_id);
  $sejours[$sejour_id] = $sejour;
}

if (!isset($sejours)) {
  if ($service_id == "NP") {
    $sejour = new CSejour();
    
    $ljoin = array();
    $ljoin["affectation"] = "affectation.sejour_id = sejour.sejour_id";
   
    $where = array();
    $where["sejour.entree"] = "<= '$date_max'";
    $where["sejour.sortie"] = ">= '$date'";
    $where["affectation.affectation_id"] = " IS NULL";
    $where["sejour.group_id"] = " = '$group_id'";
    $where["sejour.praticien_id"] = CSQLDataSource::prepareIn(array_keys($praticiens), $praticien_id);
    $where["sejour.annule"] = " = '0'";

    if ($_type_admission) {
      $where["sejour.type"] = $_type_admission == "ambucomp" ? "IN ('ambu', 'comp')" : "= '$_type_admission'";
    }
    $sejours = $sejour->loadList($where, null, null, null, $ljoin);
  
  }
  else {
    // Chargement du service
    $service->load($service_id);

    $order = "sejour.service_id, ISNULL(chambre.rank), chambre.rank, chambre.nom, ISNULL(lit.rank), lit.rank, lit.nom";

    // Chargement des sejours pour le service selectionné
    $ljoin = array();
    $ljoin["lit"] = "affectation.lit_id = lit.lit_id";
    $ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
    $ljoin["sejour"] = "affectation.sejour_id = sejour.sejour_id";

    $where = array();
    $where["affectation.sejour_id"] = "!= 0";
    $where["sejour.group_id"] = "= '$group_id'";
    $where["sejour.praticien_id"] = CSQLDataSource::prepareIn(array_keys($praticiens), $praticien_id);
    $where["sejour.annule"] = " = '0'";
    $where["affectation.entree"] = "<= '$date_max'";
    $where["affectation.sortie"] = ">= '$date'";

    if ($mode == 'instant') {
      $where[] = "affectation.effectue = '0' OR sejour.sortie_reelle >= '".CMbDT::dateTime()."'";
      $where[] = "'". CMbDT::dateTime() . "' BETWEEN affectation.entree AND affectation.sortie";
    }

    if ($_type_admission) {
      $where["sejour.type"] = $_type_admission == "ambucomp" ? "IN ('ambu', 'comp', 'ssr')" : "= '$_type_admission'";
    }

    if ($service_id) {
      $where["affectation.service_id"] = " = '$service_id'";
    }
    elseif ($function_id) {
      $ljoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
      $ljoin["secondary_function"] = "sejour.praticien_id = secondary_function.user_id";
      $where[] = "$function_id IN (users_mediboard.function_id, secondary_function.function_id)";
    }

    if ($praticien_id && $only_non_checked) {
      $where_line = array();

      $user_id = CMediusers::get()->_id;
      $where_line["sejour.entree"] = "<= '$date_max'";
      $where_line["sejour.sortie"] = ">= '$date'";
      $where_line["sejour.annule"] = " = '0'";

      $where_line["prescription.type"] = " = 'sejour'";
      $ljoin_line = array();
      $ljoin_line["prescription"] = "prescription.prescription_id = prescription_line_medicament.prescription_id";
      $ljoin_line["sejour"] = "prescription.object_id = sejour.sejour_id";
      $where_line["prescription_line_medicament.praticien_id"] = " = '$user_id'";
      $where_line["prescription_line_medicament.substituted"] = " = '0'";
      $where_line["prescription_line_medicament.variante_for_id"] = "IS NULL";
      $where_line["prescription_line_medicament.variante_active"] = " = '1'";
      
      // Lignes de médicament
      $line = new CPrescriptionLineMedicament();
      $lines = $line->loadList($where_line, null, null, null, $ljoin_line);
      /* @var CPrescriptionLineMedicament[] $lines*/
      foreach ($lines as $_line) {
        $_line->loadRefPrescription();
        $_sejour = $_line->_ref_prescription->_ref_object;
        if (!isset($sejours[$_sejour->_id])) {
          $sejours[$_sejour->_id] = $_sejour; 
        }
      }
      
      unset($where_line["prescription_line_medicament.substituted"]);
      unset($where_line["prescription_line_medicament.variante_for_id"]);
      unset($where_line["prescription_line_medicament.variante_active"]);
      
      // Lignes de commentaire
      $ljoin_line = array();
      $line = new CPrescriptionLineComment();
      $ljoin_line["prescription"] = "prescription.prescription_id = prescription_line_comment.prescription_id";
      $ljoin_line["sejour"] = "prescription.object_id = sejour.sejour_id";
      unset($where_line["prescription_line_medicament.praticien_id"]);
      $where_line["prescription_line_comment.praticien_id"] = " = '$user_id'";
      
      
      $lines = $line->loadList($where_line, null, null, null, $ljoin_line);
      
      foreach ($lines as $_line) {
        $_line->loadRefPrescription();
        $_sejour = $_line->_ref_prescription->_ref_object;
        if (!isset($sejours[$_sejour->_id])) {
          $sejours[$_sejour->_id] = $_sejour; 
        }
      }
      
      // Lignes d'éléments
      $ljoin_line = array();
      $line = new CPrescriptionLineElement();
      unset($where_line["prescription_line_comment.praticien_id"]);
      $where_line["prescription_line_element.praticien_id"] = " = '$user_id'";
      $ljoin_line["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id";
      $ljoin_line["sejour"] = "prescription.object_id = sejour.sejour_id";
      
      $lines = $line->loadList($where_line, null, null, null, $ljoin_line);
      
      foreach ($lines as $_line) {
        $_line->loadRefPrescription();
        $_sejour = $_line->_ref_prescription->_ref_object;
        if (!isset($sejours[$_sejour->_id])) {
          $sejours[$_sejour->_id] = $_sejour; 
        }
      }
      
      // Lignes mixes
      $where_line["prescription_line_mix.variante_for_id"] = "IS NULL";
      $where_line["prescription_line_mix.variante_active"] = " = '1'";
      $ljoin_line = array();
      $line_mix = new CPrescriptionLineMix();
      unset($where_line["prescription_line_element.praticien_id"]);
      $where["prescription_line_mix.praticien_id"] = " = '$user_id'";
      unset($where_line["signee"]);
      $ljoin_line["prescription"] = "prescription.prescription_id = prescription_line_mix.prescription_id";
      $ljoin_line["sejour"] = "prescription.object_id = sejour.sejour_id";
      
      $lines = $line_mix->loadList($where_line, null, null, null, $ljoin_line);
      
      foreach ($lines as $_line) {
        $_line->loadRefPrescription();
        $_sejour = $_line->_ref_prescription->_ref_object;
        if (!isset($sejours[$_sejour->_id])) {
          $sejours[$_sejour->_id] = $_sejour; 
        }
      }
    }
    else {
      $sejours = array();
      if ($service_id || $praticien_id || $function_id) {
        $affectation = new CAffectation();
        $affectations = $affectation->loadList($where, $order, null, "affectation.sejour_id", $ljoin);

        /** @var CSejour[] $sejours */
        $sejours = CStoredObject::massLoadFwdRef($affectations, "sejour_id", null, true);
        /* @var CAffectation[] $affectations*/
        foreach($affectations as $_affectation){
          $_affectation->loadRefLit()->loadCompleteView();
          $_affectation->_view = $_affectation->_ref_lit->_view;
          $sejour = $_affectation->loadRefSejour(1);
          $sejour->_ref_curr_affectation = $_affectation;
        }
      }
    }
  }
}

/* @var CPatient[] $patients */
$patients = CStoredObject::massLoadFwdRef($sejours, "patient_id");
CPatient::massLoadIPP($patients);
CStoredObject::massLoadBackRefs($patients, "dossier_medical");

CStoredObject::massLoadFwdRef($sejours, "praticien_id");
CStoredObject::massCountBackRefs($sejours, "tasks", array("realise" => "= '0'"), array(), "taches_non_realisees");
CStoredObject::massLoadBackRefs($sejours, "dossier_medical");
CSejour::massLoadSurrAffectation($sejours);
CSejour::massLoadBackRefs($sejours, "user_sejour");
CSejour::massLoadNDA($sejours);

$count_my_patient = 0;
foreach ($sejours as $sejour) {
  $count_my_patient += count($sejour->loadRefsUserSejour($userCourant));
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $sejour->checkDaysRelative($date);

  $sejour->loadRefPrescriptionSejour();
  $prescription = $sejour->_ref_prescription_sejour;
  if ($prescription->_id) {
    $prescription->loadJourOp(CMbDT::date());
  }

  // Chargement des taches non effectuées
  $sejour->_count_tasks = $sejour->_count["taches_non_realisees"];

  if ($print) {
    $task = new CSejourTask();
    $task->sejour_id = $sejour->_id;
    $task->realise = 0;
    $sejour->_ref_tasks = $task->loadMatchingList();
    foreach ($sejour->_ref_tasks as $_task) {
      $_task->loadRefPrescriptionLineElement();
    }
  }

  if ($only_non_checked && !$prescription->_id) {
    unset($sejours[$sejour->_id]);
    continue;
  }

  $sejour->_count_tasks_not_created = 0;
  $sejour->_ref_tasks_not_created = array();

  if ($prescription->_id) {
    // Chargement des lignes non associées à des taches
    $where                                         = array();
    $ljoin                                         = array();
    $ljoin["element_prescription"]                 = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
    $ljoin["sejour_task"]                          = "sejour_task.prescription_line_element_id = prescription_line_element.prescription_line_element_id";
    $where["prescription_id"]                      = " = '$prescription->_id'";
    $where["element_prescription.rdv"]             = " = '1'";
    $where["prescription_line_element.date_arret"] = " IS NULL";
    $where["active"]                               = " = '1'";
    $where[]                                       = "sejour_task.sejour_task_id IS NULL";
    $where["child_id"]                             = " IS NULL";

    $line_element                     = new CPrescriptionLineElement();
    $sejour->_count_tasks_not_created = $line_element->countList($where, null, $ljoin);

    if ($print) {
      $sejour->_ref_tasks_not_created = $line_element->loadList($where, null, null, null, $ljoin);
    }

    if ($only_non_checked) {
      $prescription->countNoValideLines($user_id);
      if ($prescription->_counts_no_valide == 0) {
        unset($sejours[$sejour->_id]);
        continue;
      }
    }

    if (@CAppUI::conf("object_handlers CPrescriptionAlerteHandler")) {
      $prescription->_count_alertes  = $prescription->countAlertsNotHandled("medium");
      $prescription->_count_urgences = $prescription->countAlertsNotHandled("high");
    }
    else {
      $prescription->countFastRecentModif();
    }
  }

  // Chargement des transmissions sur des cibles importantes
  $sejour->loadRefsTransmissions(true, null, false, 1);
  $sejour->loadRefDossierMedical();
  
  $patient = $sejour->_ref_patient;
  $patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical(false);

  if (CAppUI::conf("pharmacie Display show_risq_population", CGroups::loadCurrent()) && $lite_view && $patient->sexe == "f") {
    $patient->loadLastGrossesse();
  }

  $dossier_medical = $patient->_ref_dossier_medical;
  if ($dossier_medical->_id) {
    $dossiers[$dossier_medical->_id] = $dossier_medical;
  }
}

// Récupération des identifiants des dossiers médicaux
$dossiers_id = CMbArray::pluck($sejours, "_ref_patient", "_ref_dossier_medical", "_id");

// Suppressions des dossiers médicaux inexistants
CMbArray::removeValue("", $dossiers);

$_counts_allergie    = CDossierMedical::massCountAllergies($dossiers_id);
$_counts_antecedent  = CDossierMedical::massCountAntecedents($dossiers_id);

/* @var CDossierMedical[] $dossiers*/
foreach ($dossiers as $_dossier) {
  if ($print) {
    $_dossier->loadRefsAllergies();
    $_dossier->loadRefsAntecedents();
  }
  $_dossier->_count_allergies = array_key_exists($_dossier->_id, $_counts_allergie) ? $_counts_allergie[$_dossier->_id] : 0;
  $_dossier->_count_antecedents = array_key_exists($_dossier->_id, $_counts_antecedent) ? $_counts_antecedent[$_dossier->_id] : 0;
}

if ($service_id == "NP") {
  $sorter = CMbArray::pluck($sejours, "_ref_patient", "nom");
  array_multisort($sorter, SORT_ASC, $sejours);
}

$function = new CFunctions;
$function->load($function_id);

$praticien = new CMediusers;
$praticien->load($praticien_id);

$_sejour = new CSejour();
$_sejour->_type_admission = $_type_admission;

if ($count_my_patient && $my_patient && ($userCourant->isSageFemme() || $userCourant->isAideSoignant() || $userCourant->isInfirmiere())) {
  foreach ($sejours as $key_sejour => $_sejour) {
    if (!count($_sejour->_ref_users_sejour)) {
      unset($sejours[$key_sejour]);
    }
  }
}
if (!$count_my_patient && $my_patient) {
  $my_patient = 0;
}

$smarty = new CSmartyDP();
$smarty->assign("service"         , $service);
$smarty->assign("service_id"      , $service_id);
$smarty->assign("sejours"         , $sejours);
$smarty->assign("date"            , $date);
$smarty->assign("show_affectation", $show_affectation);
$smarty->assign("praticien"       , $praticien);
$smarty->assign("function"        , $function);
$smarty->assign("sejour_id"       , $sejour_id);
$smarty->assign("show_full_affectation", $select_view || CValue::get("show_full_affectation"));
$smarty->assign("only_non_checked", $only_non_checked);
$smarty->assign("print"           , $print);
$smarty->assign("_sejour"         , $_sejour);
$smarty->assign('ecap'            , $ecap);
$smarty->assign('mode'            , $mode);

$smarty->assign("my_patient"      , $my_patient);
$smarty->assign("count_my_patient", $count_my_patient);

$smarty->assign("select_view"     , $select_view);
if ($select_view) {
  $smarty->assign("services"    , $services);
  $smarty->assign("functions"   , $functions);
  $smarty->assign("praticiens"  , $praticiens);
  $smarty->assign("function_id" , $function_id);
  $smarty->assign("praticien_id", $praticien_id);
}

if ($sejour_id) {
  $smarty->assign("lite_view", $lite_view);

  // Rafraichissement d'un séjour  
  $sejour = reset($sejours);
  $sejour->loadRefCurrAffectation();
  
  $smarty->assign("sejour", $sejour);
  $smarty->display("../../soins/templates/inc_vw_sejour.tpl");
}
elseif ($refresh) {
  // Raffraichissement de la liste des sejours
  $smarty->display("inc_vw_sejours_global.tpl");
}
else {
  // Affichage de la liste des sejours
  $smarty->display("vw_sejours.tpl");
}
