<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id   = CValue::get("service_id");
$praticien_id = CValue::get("praticien_id");
$function_id  = CValue::get("function_id");
$sejour_id    = CValue::get("sejour_id");
$show_affectation = CValue::get("show_affectation", false);
$only_non_checked = CValue::get("only_non_checked", 0);
$print        = CValue::get("print", false);
$_type_admission = CValue::getOrSession("_type_admission", "");
$select_view = CValue::get("select_view", false);

// Mode Dossier de soins, chargement de la liste des service, praticiens, functions
$services = array();
$functions = array();
$praticiens = array();

if ($select_view || (!$service_id && !$praticien_id && !$function_id && !$sejour_id)) {
  // Redirection pour gérer le cas ou le volet par defaut est l'autre affichage des sejours
  if (CAppUI::pref("vue_sejours") == "standard") {
    CAppUI::redirect("m=soins&tab=vw_idx_sejour");
  }

  // Récupération d'un éventuel service_id en session
  $service_id = CValue::getOrSession("service_id");

  // Récupération d'un éventuel praticien_id en session
  if (!$service_id) {
    $praticien_id = CValue::getOrSession("praticien_id");
  }

  $select_view = true;

  $service = new CService();
  $services = $service->loadListWithPerms();

  $praticien = new CMediusers;
  $praticiens = $praticien->loadPraticiens();

  $function = new CFunctions();
  $functions = $function->loadSpecialites();
}

$date     = CMbDT::date();
$date_max = CMbDT::date("+ 1 DAY", $date);
$service  = new CService();
$user_id  = CAppUI::$user->_id;
$group_id = CGroups::loadCurrent()->_id;

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
    $where["sejour.annule"] = " = '0'";

    if ($_type_admission) {
      $where["sejour.type"] = $_type_admission == "ambucomp" ? "IN ('ambu', 'comp')" : "= '$_type_admission'";
    }
    $sejours = $sejour->loadList($where, null, null, null, $ljoin);
  
  } else {
    // Chargement du service
    $service->load($service_id);
    
    // Chargement des sejours pour le service selectionné
    $affectation = new CAffectation();

    $ljoin = array();

    $where = array();
    $where["affectation.sejour_id"] = "!= 0";
    $where["sejour.group_id"] = "= '$group_id'";
    if ($_type_admission) {
      $where["sejour.type"] = $_type_admission == "ambucomp" ? "IN ('ambu', 'comp')" : "= '$_type_admission'";
    }

    if ($service_id) {
      $where["affectation.entree"] = "<= '$date_max'";
      $where["affectation.sortie"] = ">= '$date'";
      $ljoin["sejour"] = "affectation.sejour_id = sejour.sejour_id";
      $where["sejour.annule"] = " = '0'";
      $where["affectation.service_id"] = " = '$service_id'";
    }
    elseif ($praticien_id && !$only_non_checked) {
      $where["affectation.entree"] = "<= '$date_max'";
      $where["affectation.sortie"] = ">= '$date'";
      $ljoin["sejour"] = "affectation.sejour_id = sejour.sejour_id";
      $where["sejour.annule"] = " = '0'";
      
      $where["sejour.praticien_id"] = " = '$praticien_id'";
    }
    elseif ($function_id) {
      $where["affectation.entree"] = "<= '$date_max'";
      $where["affectation.sortie"] = ">= '$date'";
      $ljoin["sejour"] = "affectation.sejour_id = sejour.sejour_id";
      $where["sejour.annule"] = " = '0'";
      
      $ljoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
      $ljoin["secondary_function"] = "sejour.praticien_id = secondary_function.user_id";
      $where[] = "$function_id IN (users_mediboard.function_id, secondary_function.function_id)";
    }
    
    if ($praticien_id && $only_non_checked) {
      $where_line = array();
      
      $user_id = CAppUI::$user->_id;
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
      $line = new CPrescriptionLineMedicament;
      $lines = $line->loadList($where_line, null, null, null, $ljoin_line);
      
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
      $line = new CPrescriptionLineComment;
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
      $line = new CPrescriptionLineElement;
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
      $line_mix = new CPrescriptionLineMix;
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
        $affectations = $affectation->loadList($where, null, null, null, $ljoin);

        CMbObject::massLoadFwdRef($affectations, "sejour_id");

        foreach($affectations as $_affectation){
          $_affectation->loadRefLit()->loadCompleteView();
          $_affectation->_view = $_affectation->_ref_lit->_view;
          $sejour = $_affectation->loadRefSejour(1);
          $sejour->_ref_curr_affectation = $_affectation;
        }

        $sorter = CMbArray::pluck($affectations, "_ref_lit", "_view");
        array_multisort($sorter, SORT_ASC, $affectations);
        $sejours = CMbArray::pluck($affectations, "_ref_sejour");
      }
    }
  }
}

CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($sejours, "praticien_id");

foreach ($sejours as $sejour) {
  $sejour->loadRefPatient(1)->loadIPP();
  $sejour->loadRefPraticien(1);
  $sejour->checkDaysRelative($date);
  $sejour->loadSurrAffectations();
  $sejour->loadNDA();

  $sejour->loadRefPrescriptionSejour();
  $prescription = $sejour->_ref_prescription_sejour;
  if ($prescription->_id) {
    $prescription->loadJourOp(CMbDT::date());
  }
  // Chargement des taches non effectuées
  $task = new CSejourTask();
  $task->sejour_id = $sejour->_id;
  $task->realise = 0;
  $sejour->_count_tasks = $task->countMatchingList();
  
  if ($print) {
    $sejour->_ref_tasks = $task->loadMatchingList();
    foreach ($sejour->_ref_tasks as $_task) {
      $_task->loadRefPrescriptionLineElement();
    }  
  }
  
  // Chargement des lignes non associées à des taches
  $where = array();
  $ljoin = array();
  $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
  $ljoin["sejour_task"] = "sejour_task.prescription_line_element_id = prescription_line_element.prescription_line_element_id";
  $where["prescription_id"] = " = '$prescription->_id'";
  $where["element_prescription.rdv"] = " = '1'";
  $where["active"] = " = '1'";
  $where[] = "sejour_task.sejour_task_id IS NULL";
  $where["child_id"] = " IS NULL";

  $line_element = new CPrescriptionLineElement();
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
    $prescription->_count_alertes = $prescription->countAlertsNotHandled("medium");
    $prescription->_count_urgences = $prescription->countAlertsNotHandled("high");
  }
  else {
    $prescription->countFastRecentModif();
  }
    
  // Chargement des transmissions sur des cibles importantes
  $sejour->loadRefsTransmissions(true, null, false, 1);
  
  $patient = $sejour->_ref_patient;
  $patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical();
  $dossier_medical = $patient->_ref_dossier_medical;
  
  if ($dossier_medical->_id) {
    $dossier_medical->loadRefsAllergies();
    $dossier_medical->loadRefsAntecedents();
    $dossier_medical->countAntecedents();
    $dossier_medical->countAllergies();
  }
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

$smarty = new CSmartyDP;
$smarty->assign("service"         , $service);
$smarty->assign("service_id"      , $service_id);
$smarty->assign("sejours"         , $sejours);
$smarty->assign("date"            , $date);
$smarty->assign("show_affectation", $show_affectation);
$smarty->assign("praticien"       , $praticien);
$smarty->assign("function"        , $function);
$smarty->assign("sejour_id"       , $sejour_id);
$smarty->assign("show_full_affectation", $select_view);
$smarty->assign("only_non_checked", $only_non_checked);
$smarty->assign("print"           , $print);
$smarty->assign("_sejour"         , $_sejour);

$smarty->assign("select_view"     , $select_view);
if ($select_view) {
  $smarty->assign("services"  , $services);
  $smarty->assign("functions" , $functions);
  $smarty->assign("praticiens", $praticiens);
  $smarty->assign("function_id", $function_id);
  $smarty->assign("praticien_id", $praticien_id);
}

if ($sejour_id) {
  // Rafraichissement d'un séjour  
  $sejour = reset($sejours);
  $sejour->loadRefCurrAffectation();
  
  $smarty->assign("sejour", $sejour);
  $smarty->display("../../soins/templates/inc_vw_sejour.tpl");
} 

else {
  // Rafraichissement de la liste des sejours
  $smarty->display("vw_sejours.tpl");
}
