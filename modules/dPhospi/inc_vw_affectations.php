<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/


/**
 * Retourne une référence sur un praticien donné, 
 * après mise en cache si nécessaire
 */
function & getCachedPraticien($praticien_id) {
  static $listPraticiens = array();
  
  if (!array_key_exists($praticien_id, $listPraticiens)) {
    $praticien = new CMediusers;
    $praticien->load($praticien_id);
    $praticien->_ref_function =& getCachedFunction($praticien->function_id);
    $listPraticiens[$praticien_id] =& $praticien;
  }
  
  return $listPraticiens[$praticien_id];  
}

/**
 * Retourne une référence sur une fonction donnée, 
 * après mise en cache si nécessaire
 */
function & getCachedFunction($function_id) {
  static $listFunctions = array();
  
  if (!array_key_exists($function_id, $listFunctions)) {
    $function = new CFunctions;
    $function->load($function_id);
    $listFunctions[$function_id] =& $function;
  }
  
  return $listFunctions[$function_id];  
}

/**
 * Retourne une référence sur un patient donné, 
 * après mise en cache si nécessaire
 */
function & getCachedPatient($patient_id) {
  static $listPatients = array();
  
  if (!array_key_exists($patient_id, $listPatients)) {
    $patient = new CPatient;
    $patient->load($patient_id);
    $listPatients[$patient_id] =& $patient;
  } 
  
  return $listPatients[$patient_id];  
}

/**
 * Retourne une référence sur un lit donné, 
 * après mise en cache si nécessaire
 */
function &getCachedLit($lit_id) {
  static $listLits = array();
  
  if (!array_key_exists($lit_id, $listLits)) {
    $lit = new CLit;
    $lit->load($lit_id);
    $lit->loadRefChambre();
    $listLits[$lit_id] =& $lit;
  }

  return $listLits[$lit_id];  
}

/**
 * Charge complètement un service pour l'affichage des affectations
 */
function loadServiceComplet(&$service, $date, $mode, $praticien_id = "", $type = "", $prestation_id = "") {

  $service->loadRefsBack();
  $service->_nb_lits_dispo = 0;
  $dossiers = array();
  $systeme_presta = CAppUI::conf("dPhospi systeme_prestations");

  foreach ($service->_ref_chambres as $chambre_id => &$chambre) {
    $chambre->loadRefsBack();

    /** @var CLit $lit */
    foreach ($chambre->_ref_lits as $lit_id => &$lit) {
      $lit->loadAffectations($date);

      foreach ($lit->_ref_affectations as $affectation_id => &$affectation) {
        if (!$affectation->effectue || $mode) {
          $affectation->loadRefSejour();
          if ($praticien_id) {
            if ($affectation->_ref_sejour->praticien_id != $praticien_id) {
              unset($lit->_ref_affectations[$affectation_id]);
              continue;
            }
          }
          if ($type) {
            if ($affectation->_ref_sejour->type != $type) {
              unset($lit->_ref_affectations[$affectation_id]);
              continue;
            }
          }
          
          $affectation->loadRefsAffectations();
          $affectation->checkDaysRelative($date);

          $aff_prev =& $affectation->_ref_prev;
          if ($aff_prev->affectation_id) {
            $aff_prev->_ref_lit =& getCachedLit($aff_prev->lit_id);
          }

          $aff_next =& $affectation->_ref_next;
          if ($aff_next->affectation_id) {
            $aff_next->_ref_lit =& getCachedLit($aff_next->lit_id);
          }

          $sejour =& $affectation->_ref_sejour;
          $sejour->loadRefPrestation();
          $sejour->loadRefsOperations();
          $sejour->loadNDA();
          $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
          $sejour->_ref_patient =& getCachedPatient($sejour->patient_id);
          $sejour->_ref_patient->loadRefDossierMedical(false);
          
          // Chargement des droits CMU
          $sejour->getDroitsCMU();

          foreach ($sejour->_ref_operations as $operation_id => $curr_operation) {
            $sejour->_ref_operations[$operation_id]->loadExtCodesCCAM();
          }
          $chambre->_nb_affectations++;
          $dossiers[] = $sejour->_ref_patient->_ref_dossier_medical;

          if ($systeme_presta == "expert") {
            if ($prestation_id) {
              $sejour->loadLiaisonsForDay($prestation_id, $date);
            }
          }
        }
        else {
          unset($lit->_ref_affectations[$affectation_id]);
        }
      }
    }

    CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

    if (!$service->externe) {
      $chambre->checkChambre();
      $service->_nb_lits_dispo += ($chambre->annule == 0 ? $chambre->_nb_lits_dispo : 0);
    }
  }
}

/**
 *  Chargement des admissions à affecter
 */
function loadSejourNonAffectes($where, $order = null, $praticien_id = null, $prestation_id = null) {
  $group_id = CGroups::loadCurrent()->_id;
  $systeme_presta = CAppUI::conf("dPhospi systeme_prestations");

  $leftjoin = array(
    "affectation"     => "sejour.sejour_id = affectation.sejour_id",
    "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
    "patients"        => "sejour.patient_id = patients.patient_id"
  );

  if ($praticien_id) {
    $where["sejour.praticien_id"] = " = '$praticien_id'";
  }

  $where["sejour.group_id"] = "= '$group_id'";
  
  $where[] = "(sejour.type != 'seances' && affectation.affectation_id IS NULL) OR sejour.type = 'seances'";
  
  if ($order == null) {
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  }

  $sejourNonAffectes = new CSejour();
  $sejourNonAffectes = $sejourNonAffectes->loadList($where, $order, 100, null, $leftjoin);

  /** @var $sejourNonAffectes CSejour[] */
  foreach ($sejourNonAffectes as $sejour) {
    $sejour->loadRefPrestation();
    $sejour->loadNDA();
    $sejour->loadRefsPrescriptions();
    $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
    $sejour->_ref_patient   =& getCachedPatient($sejour->patient_id);
    $sejour->_ref_patient->loadRefDossierMedical(false);

    if ($systeme_presta == "expert" && $prestation_id) {
      $sejour->loadLiaisonsForPrestation($prestation_id);
    }

    // Chargement des droits CMU
    $sejour->getDroitsCMU();

    // Chargement des opérations
    $sejour->loadRefsOperations();
    foreach ($sejour->_ref_operations as $_operation) {
      $_operation->loadExtCodesCCAM();
    }
  }

  $dossiers = CMbArray::pluck($sejourNonAffectes, "_ref_patient", "_ref_dossier_medical");

  CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

  return $sejourNonAffectes;
}

/**
 * Chargement des affectations dans les couloirs
 */
function loadAffectationsCouloirs($where, $order = null, $praticien_id = null, $prestation_id = null) {
  $group_id = CGroups::loadCurrent()->_id;
  $systeme_presta = CAppUI::conf("dPhospi systeme_prestations");

  $ljoin = array(
    "sejour"          => "affectation.sejour_id = sejour.sejour_id",
    "patients"        => "sejour.patient_id = patients.patient_id",
    "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
  );
  
  if ($praticien_id) {
    $where["sejour.praticien_id"] = " = '$praticien_id'";
  }
  
  if ($order == null) {
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  }
  
  $where["affectation.lit_id"] = " IS NULL";
  
  $affectation = new CAffectation;
  $affectations = $affectation->loadList($where, $order, null, null, $ljoin);
  
  $tab_affectations = array();
  
  foreach ($affectations as $affectation) {
    $affectation->loadRefsAffectations();
    $affectation->loadRefSejour()->loadRefPatient();
    $aff_prev =& $affectation->_ref_prev;
    if ($aff_prev->affectation_id) {
      $aff_prev->_ref_lit =& getCachedLit($aff_prev->lit_id);
    }

    $aff_next =& $affectation->_ref_next;
    if ($aff_next->affectation_id) {
      $aff_next->_ref_lit =& getCachedLit($aff_next->lit_id);
    }

    $sejour =& $affectation->_ref_sejour;
    $sejour->loadRefPrestation();
    $sejour->loadRefsOperations();
    $sejour->loadNDA();
    $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
    $sejour->_ref_patient =& getCachedPatient($sejour->patient_id);
    $sejour->_ref_patient->loadRefDossierMedical(false);

    if ($systeme_presta == "expert" && $prestation_id) {
      $sejour->loadLiaisonsForPrestation($prestation_id);
    }
    // Chargement des droits CMU
    $sejour->getDroitsCMU();

    foreach ($sejour->_ref_operations as $operation_id => $curr_operation) {
      $sejour->_ref_operations[$operation_id]->loadExtCodesCCAM();
    }
    $tab_affectations[$affectation->service_id][] = $affectation;
  }
  $dossiers = CMbArray::pluck($affectations, "_ref_sejour", "_ref_patient", "_ref_dossier_medical");
  CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");

  return $tab_affectations;
}

function loadVueTempo(&$objects = array(), $suivi_affectation, $lits = array(), &$operations = array(), $date_min, $date_max, $period, $prestation_id, &$functions_filter = null, $filter_function = null, &$sejours_non_affectes = null) {
  $maternite_active = CModule::getActive("maternite");

  foreach ($objects as $key => $_object) {
    $_object->_entree = $_object->entree;
    $_object->_sortie = $_object->sortie;

    switch (get_class($_object)) {
      case "CAffectation":
        if ($_object->_is_prolong) {
          $_object->_sortie = CMbDT::dateTime();
        }

        $_object->loadRefsAffectations();
        $_object->_affectations_enfant_ids = CMbArray::pluck($_object->loadBackRefs("affectations_enfant"), "affectation_id");
        /** @var CSejour $sejour **/
        $sejour = $_object->loadRefSejour();

        if (!$suivi_affectation && $_object->parent_affectation_id) {
          $suivi_affectation = true;
        }

        $lits[$_object->lit_id]->_ref_affectations[$_object->_id] = $_object;

        if ($_object->_is_prolong) {
          $_object->_start_prolongation = CMbDate::position(max($date_min, $_object->_entree), $date_min, $period);
          $_object->_end_prolongation   = CMbDate::position(min($date_max, $_object->_sortie), $date_min, $period);
          $_object->_width_prolongation = $_object->_end_prolongation - $_object->_start_prolongation;
        }

        break;
      case "CSejour":
        $sejour = $_object;
    }

    $sejour->loadRefPraticien()->loadRefFunction();

    if (is_array($functions_filter)) {
      $functions_filter[$sejour->_ref_praticien->function_id] = $sejour->_ref_praticien->_ref_function;
      if ($filter_function && $filter_function != $sejour->_ref_praticien->function_id) {
        unset($objects[$_object->_id]);
        continue;
      }
    }

    $sejour->loadRefPrestation();
    $sejour->loadRefChargePriceIndicator();
    $patient = $sejour->loadRefPatient();
    $patient->loadRefPhotoIdentite();
    $constantes = $patient->getFirstConstantes();
    $patient->_overweight = $constantes->poids > 120;
    $patient->loadRefDossierMedical(false);

    $_object->_entree_offset = CMbDate::position(max($date_min, $_object->_entree), $date_min, $period);
    $_object->_sortie_offset = CMbDate::position(min($date_max, $_object->_sortie), $date_min, $period);
    $_object->_width = $_object->_sortie_offset - $_object->_entree_offset;

    if ($_object->_width === 0) {
      $_object->_width = 0.01;
    }

    if (!isset($operations[$sejour->_id])) {
      $operations[$sejour->_id] = $sejour->loadRefsOperations();
    }

    if ($prestation_id) {
      $sejour->loadRefFirstLiaisonForPrestation($prestation_id);
      $sejour->loadLiaisonsForPrestation($prestation_id, CMbDT::date($date_min), CMbDT::date($date_max));
    }

    if ($maternite_active && $sejour->grossesse_id) {
      $sejour->_sejours_enfants_ids = CMbArray::pluck($sejour->loadRefsNaissances(), "sejour_enfant_id");
    }

    foreach ($operations[$sejour->_id] as $key=>$_operation) {
      $_operation->loadRefPlageOp(1);
      $hour_operation = CMbDT::format($_operation->temp_operation, "%H");
      $min_operation = CMbDT::format($_operation->temp_operation, "%M");
      $fin_operation = CMbDT::dateTime("+$hour_operation hours +$min_operation minutes", $_operation->_datetime_best);

      if ($_operation->_datetime_best > $date_max || $fin_operation < $date_min) {
        unset($sejour->_ref_operations[$_operation->_id]);
        continue;
      }

      $_operation->_debut_offset[$_object->_id] = CMbDate::position($_operation->_datetime_best, max($date_min, $_object->_entree), $period);

      $_operation->_fin_offset[$_object->_id] = CMbDate::position($fin_operation, max($date_min, $_object->_entree), $period);
      $_operation->_width[$_object->_id] = $_operation->_fin_offset[$_object->_id] - $_operation->_debut_offset[$_object->_id];

      if (($_operation->_datetime_best > $date_max)) {
        $_operation->_width_uscpo[$_object->_id] = 0;
      }
      else {
        $fin_uscpo = $hour_operation + 24 * $_operation->duree_uscpo;
        $_operation->_width_uscpo[$_object->_id] = CMbDate::position(CMbDT::dateTime("+$fin_uscpo hours + $min_operation minutes", $_operation->_datetime_best), max($date_min, $_object->_entree), $period) - $_operation->_fin_offset[$_object->_id];
      }
    }

    if (is_array($sejours_non_affectes)) {
      $lit = new CLit();
      $lit->_selected_item = new CItemPrestation();
      $lit->_lines = array();
      if ($_object instanceof CAffectation) {
        $lit->_affectation_id = $_object->_id;
        $lit->_lines[] = $_object->_id;
      }
      else {
        $lit->_sejour_id = $_object->_id;
        $lit->_lines[] = $_object->_guid;
      }

      @$sejours_non_affectes[$_object->service_id ? $_object->service_id : "np"][] = $lit;
    }
  }
}