<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Charge complètement un service pour l'affichage des affectations
 *
 * @param CService $service       le service concerné
 * @param string   $date          le filtre de date sur les affectations
 * @param string   $mode          forcer le chargement des affectations effectuées
 * @param int      $praticien     charge les séjours pour un praticien en particulier
 * @param string   $type          charge les séjours pour un type d'hospitalisation
 * @param int      $prestation_id charge la prestation éventuellement associée à chaque séjour
 *
 *
 * @return void
 */
function loadServiceComplet(&$service, $date, $mode, $praticien_id = "", $type = "", $prestation_id = "", $with_dossier_medical = true) {
  $service->_nb_lits_dispo = 0;
  $dossiers = array();
  $systeme_presta = CAppUI::conf("dPhospi systeme_prestations");

  $lits = $service->loadRefsLits();

  foreach ($lits as $_lit) {
    $_lit->_ref_affectations = array();
    $_lit->checkDispo($date);
  }

  $affectations = $service->loadRefsAffectations($date, $mode, false, true);

  $sejours = CMbObject::massLoadFwdRef($affectations, "sejour_id");
  CMbObject::massLoadFwdRef($sejours, "patient_id");
  CMbObject::massLoadFwdRef($sejours, "prestation_id");
  CMbObject::massLoadFwdRef($sejours, "praticien_id");

  if (CModule::getActive("dPImeds")) {
    CSejour::massLoadNDA($sejours);
  }

  foreach ($affectations as $_affectation) {
    $sejour = $_affectation->loadRefSejour();
    if ($praticien_id) {
      if ($sejour->praticien_id != $praticien_id) {
        unset($affectations[$_affectation->_id]);
        continue;
      }
    }
    if ($type) {
      if ($sejour->type != $type) {
        unset($affectations[$_affectation->_id]);
        continue;
      }
    }

    $lits[$_affectation->lit_id]->_ref_affectations[$_affectation->_id] = $_affectation;

    $_affectation->loadRefsAffectations(true);

    $_affectation->checkDaysRelative($date);

    $aff_prev = $_affectation->_ref_prev;
    if ($aff_prev->_id) {
      if ($aff_prev->lit_id) {
        $aff_prev->loadRefLit();
      }
      else {
        $aff_prev->loadRefService();
      }
    }

    $aff_next = $_affectation->_ref_next;
    if ($aff_next->_id) {
      if ($aff_next->lit_id) {
        $aff_prev->loadRefLit();
      }
      else {
        $aff_prev->loadRefService();
      }
    }

    $sejour->loadRefPrestation();
    $sejour->loadRefsOperations();
    $sejour->loadNDA();
    $sejour->loadRefPraticien();
    $sejour->loadRefPatient();

    if ($with_dossier_medical) {
      $sejour->_ref_patient->loadRefDossierMedical(false);
      $dossiers[] = $sejour->_ref_patient->_ref_dossier_medical;
    }

    // Chargement des droits CMU
    $sejour->getDroitsCMU();

    foreach ($sejour->_ref_operations as $_operation) {
      $_operation->loadExtCodesCCAM();
    }

    $_affectation->_ref_lit = $lits[$_affectation->lit_id];

    $_affectation->loadRefLit();

    $_affectation
      ->_ref_lit
      ->_ref_chambre
      ->_nb_affectations++;

    if ($systeme_presta == "expert" && $prestation_id) {
      $sejour->loadLiaisonsForDay($prestation_id, $date);
    }
  }

  foreach ($lits as $_lit) {
    array_multisort(CMbArray::pluck($_lit->_ref_affectations, "_ref_sejour", "entree"), SORT_ASC, $_lit->_ref_affectations);
  }

  if ($with_dossier_medical) {
    CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");
  }

  if (!$service->externe) {
    foreach ($service->_ref_chambres as $_chambre) {
      $_chambre->checkChambre();
      $service->_nb_lits_dispo += $_chambre->_nb_lits_dispo;
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
    "users"           => "users.user_id = users_mediboard.user_id",
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

  CMbObject::massLoadFwdRef($sejourNonAffectes, "prestation_id");
  CMbObject::massLoadFwdRef($sejourNonAffectes, "praticien_id");
  CMbObject::massLoadFwdRef($sejourNonAffectes, "patient_id");

  /** @var $sejourNonAffectes CSejour[] */
  foreach ($sejourNonAffectes as $sejour) {
    $sejour->loadRefPrestation();
    $sejour->loadNDA();
    $sejour->loadRefPraticien();
    $sejour->loadRefPatient();
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
    "users"           => "users.user_id = users_mediboard.user_id",
  );
  
  if ($praticien_id) {
    $where["sejour.praticien_id"] = " = '$praticien_id'";
  }

  if ($order == null) {
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  }
  
  $where["affectation.lit_id"] = " IS NULL";
  
  $affectation = new CAffectation;
  /* @var CAffectation[] $affectations*/
  $affectations = $affectation->loadList($where, $order, null, null, $ljoin);

  $sejours = CMbObject::massLoadFwdRef($affectations, "sejour_id");

  CMbObject::massLoadFwdRef($sejours, "praticien_id");
  CMbObject::massLoadFwdRef($sejours, "patient_id");

  $tab_affectations = array();
  
  foreach ($affectations as $affectation) {
    $affectation->loadRefsAffectations();
    $affectation->loadRefSejour()->loadRefPatient();
    $affectation->_ref_prev->loadRefLit();
    $affectation->_ref_next->loadRefLit();

    $sejour =& $affectation->_ref_sejour;
    $sejour->loadRefPrestation();
    $sejour->loadRefsOperations();
    $sejour->loadNDA();
    $sejour->loadRefPraticien();
    $sejour->loadRefPatient();
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

  foreach ($objects as $_object) {
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
      $sejour->loadLiaisonsForPrestation($prestation_id, CMbDT::date(max($date_min, $_object->_entree)), CMbDT::date(min($date_max, $_object->_sortie)));
      if ($_object->_class == "CAffectation") {
        $_object->_liaisons_for_prestation = $sejour->_liaisons_for_prestation;
      }
    }

    if ($maternite_active && $sejour->grossesse_id) {
      $sejour->_sejours_enfants_ids = CMbArray::pluck($sejour->loadRefsNaissances(), "sejour_enfant_id");
    }

    foreach ($operations[$sejour->_id] as $_operation) {
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