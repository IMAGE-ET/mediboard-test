<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

if (CMedicament::getBase() == "vidal") {
  CAppUI::stepMessage(UI_MSG_WARNING, "Cette fonctionnalité n'est pas encore présente.");
  CApp::rip();
}

$group = CGroups::loadCurrent();
$sejour_id          = CValue::getOrSession("sejour_id");
$date               = CValue::getOrSession("date_plan_soins");
$nb_decalage        = CValue::get("nb_decalage");
$mode_dossier       = CValue::get("mode_dossier", "administration");
$chapitre           = CValue::get("chapitre"); // Chapitre a rafraichir
$object_id          = CValue::get("object_id");
$object_class       = CValue::get("object_class");
$unite_prise        = CValue::get("unite_prise");
$without_check_date = CValue::get("without_check_date", "0");
$hide_close         = CValue::get("hide_close", 0);
$with_navigation    = CValue::get("with_navigation");
$regroup_lines      = CValue::get("regroup_lines");
$hide_old_lines     = CValue::get("hide_old_lines", CAppUI::conf("soins suivi hide_old_line", $group->_guid));
$hide_line_inactive = CValue::get("hide_line_inactive", CAppUI::pref("hide_line_inactive"));

if (!$date) {
  $date = CMbDT::date();
}

if ($date != CMbDT::date()) {
  $hide_old_lines = 0;
  $hide_line_inactive = 0;
}

CPrescription::$mode_plan_soins = true;

// Permet de gerer le cas ou des unites de prises contiennent des '
$unite_prise = stripslashes(preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $unite_prise));

// Recuperation du sejour_id si seulement l'object est passé
if ($object_id && $object_class) {
  $object = new $object_class();
  $object->load($object_id);
  $sejour_id = $object->_ref_prescription->object_id;
}

// Initialisations
$operation = new COperation();
$operations = array();

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefCurrAffectation();
$sejour->_ref_curr_affectation->loadView();

if ($group->_id != $sejour->group_id) {
  CAppUI::stepAjax("Ce séjour n'est pas dans l'établissement courant", UI_MSG_WARNING);
  return;
}

$planif_manuelle = CAppUI::conf("dPprescription CPrescription planif_manuelle", $group->_guid);

$sejour->loadRefPatient();
$sejour->loadRefPraticien();

// Chargement des caracteristiques du patient
$patient =& $sejour->_ref_patient;
$patient->loadRefPhotoIdentite();
$patient->loadRefLatestConstantes(null, array("poids", "taille"));

$patient->loadRefDossierMedical();
$dossier_medical = $patient->_ref_dossier_medical;

if ($dossier_medical->_id) {
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();
}

$const_med = $patient->_ref_constantes_medicales;
$poids = $const_med->poids;
$risques_cis = array();

global $atc_classes;
$atc_classes = array();
$hidden_lines_count = 0;
$hide_inactive_count = 0;
if (CModule::getActive("dPprescription")) {

  // Chargement des cis à risque
  $where = array();
  $where["risque"]    = " = '1'";
  $risques_cis = CProduitLivretTherapeutique::getCISList($where);

  // Chargement de la prescription à partir du sejour
  $prescription = new CPrescription();
  $prescription->object_id = $sejour_id;
  $prescription->object_class = "CSejour";
  $prescription->type = "sejour";
  $prescription->loadMatchingObject();

  // Chargement de toutes les planifs systemes si celles-ci ne sont pas deja chargées
  $prescription->calculAllPlanifSysteme();

  // Chargement des configs de service
  $sejour->loadRefCurrAffectation($date);

  if (!$sejour->_ref_curr_affectation->_id) {
    $sejour->loadRefsAffectations();
    $sejour->_ref_curr_affectation = $sejour->_ref_last_affectation;
  }

  if ($sejour->_ref_curr_affectation->_id) {
    $service_id = $sejour->_ref_curr_affectation->service_id;
  }
  else {
    $service_id = "none";
  }

  $configs = CConfigService::getAllFor($service_id);

  if (!$nb_decalage) {
    $nb_decalage = $configs["Nombre postes avant"];
  }

  if (!$without_check_date && !($object_id && $object_class) && !$chapitre) {
    // Si la date actuelle est inférieure a l'heure affichée sur le plan de soins, on affiche le plan de soins de la veille
    $datetime_limit = CMbDT::dateTime($configs["Poste 1"].":00:00");
    if (CMbDT::dateTime() < $datetime_limit) {
      $date = CMbDT::date("- 1 DAY");
    }
    else {
      $date = CMbDT::date();
    }
  }

  $prescription->loadJourOp($date);

  $composition_dossier = array();
  $bornes_composition_dossier = array();
  $count_composition_dossier = array();

  $tabHours = CAdministration::getTimingPlanSoins($date, $configs);

  foreach ($tabHours as $_key_date => $_period_date) {
    foreach ($_period_date as $_key_periode => $_period_dates) {
      $count_composition_dossier[$_key_date][$_key_periode] = $planif_manuelle ? 3 : 2;
      $first_date = reset(array_keys($_period_dates));
      $first_time = reset(reset($_period_dates));
      $last_date = end(array_keys($_period_dates));
      $last_time = end(end($_period_dates));

      $composition_dossier[] = "$_key_date-$_key_periode";

      $bornes_composition_dossier["$_key_date-$_key_periode"]["min"] = "$first_date $first_time:00:00";
      $bornes_composition_dossier["$_key_date-$_key_periode"]["max"] = "$last_date $last_time:00:00";


      foreach ($_period_dates as $_key_real_date => $_period_hours) {
        $count_composition_dossier[$_key_date][$_key_periode] += count($_period_hours);
        $_dates[$_key_real_date] = $_key_real_date;
      }
    }
  }

  // Calcul du dossier de soin pour une ligne
  if ($object_id && $object_class) {
    // Chargement de la ligne de prescription
    $line = new $object_class;
    $line->load($object_id);

    switch ($line->_class) {
      case "CPrescriptionLineMedicament":
        $line->countVariantes();
        $line->countBackRefs("administration");
        $line->loadRefsVariantes();
        if ($line->delay_prise) {
          $line->loadRefLastAdministration();
        }
        $line->_ref_produit->loadRefsFichesATC();
        if (!$line->_fin_reelle) {
          $line->_fin_reelle = $prescription->_ref_object->_sortie;
        }
        $line->calculPrises($prescription, $_dates, null, null, true, $planif_manuelle);
        $line->calculAdministrations($_dates);
        $line->removePrisesPlanif();
        break;
      case "CPrescriptionLineElement":
        $element = $line->_ref_element_prescription;
        $name_cat = $element->category_prescription_id;
        $element->loadRefCategory();
        $name_chap = $element->_ref_category_prescription->chapitre;
        $line->calculAdministrations($_dates);
        $line->calculPrises($prescription, $_dates, $name_chap, $name_cat, true, $planif_manuelle);
        $line->removePrisesPlanif();
        break;
      case "CPrescriptionLineMix":
        $line->countVariantes();
        $line->loadRefsVariantes();
        $line->loadRefsLines();
        $line->loadVoies();
        $line->loadRefPraticien();
        $line->loadRefLogSignaturePrat();
        $line->loadRefsPrises();
        if (CAppUI::conf("dPprescription CPrescription show_initiales_pharma", $group->_guid)) {
          $line->loadRefUserValidationPharma();
        }
        $line->calculVariations();

        // Calcul des prises prevues
        $line->calculQuantiteTotal();
        foreach ($_dates as $curr_date) {
          $line->calculPrisesPrevues($curr_date, $planif_manuelle);
        }
        $line->calculAdministrations();
        $line->updateAlerteAntibio();

        if ($line->sans_planif) {
          foreach ($line->_ref_lines as $_line_mix) {
            if ($_line_mix->delay_prise) {
              $_line_mix->loadRefLastAdministration();
            }
          }
        }

        // Chargement des transmissions de la prescription_line_mix
        $transmission = new CTransmissionMedicale();
        $transmission->object_class = "CPrescriptionLineMix";
        $transmission->object_id = $line->_id;
        $transmission->sejour_id = $sejour->_id;
        $transmissions = $transmission->loadMatchingList();

        foreach ($transmissions as $_transmission) {
          $_transmission->loadRefsFwd();
          if ($_transmission->object_id && $_transmission->object_class) {
            $prescription->_transmissions[$_transmission->object_class][$_transmission->object_id][$_transmission->_id] = $_transmission;
          }
        }
    }

    $line->countPlanifications();

    if (in_array($line->jour_decalage, array("ER", "R"))) {
      $line->loadRefOperation();
    }
    $line->loadActiveDates();
  }
  else {
    // Calcul du dossier de soin complet
    if ($prescription->_id) {
      $show_initiales_pharma = CAppUI::conf("dPprescription CPrescription show_initiales_pharma", $group->_guid);

      // Pour le bouton de gestion des traitements personnels
      $patient = $prescription->loadRefPatient();
      $prescription_tp = $patient->loadRefDossierMedical()->loadUniqueBackRef("prescription");
      $prescription->_count_dossier_medical_tp = 0;
      $_line_tp = new CPrescriptionLineMedicament();
      $where = array();
      if ($prescription_tp->_id) {
        $where["prescription_id"] = " = '$prescription_tp->_id'";
        $where[] = "fin IS NULL OR fin > '".CMbDT::date()."'";
        $prescription->_count_dossier_medical_tp = $_line_tp->countList($where);
      }
      $prescription->countLinesTP();

      if (CAppUI::conf("dPprescription CPrescription group_perf_atc", $group) && CAppUI::pref("regroupement_med_plan_soins")) {
        $atc_classes["other_perf"] = "Autres perfusions";
      }
      $display_level = $prescription->_display_level ? $prescription->_display_level : '2';
      $_ref_name = "_ref_ATC_".$display_level."_code";

      // Chargement des lignes de medicament
      if (in_array($chapitre, array("all_med", "all_chaps"))) {
        $prescription->loadRefsLinesMedByCat("1", "1", '', $hide_old_lines, null, null, $hide_line_inactive);
        foreach ($prescription->_ref_prescription_lines as $_line_med) {
          $_line_med->loadRefLogSignee();
          $_line_med->countVariantes();
          $_line_med->countBackRefs("administration");
          $_line_med->loadRefsVariantes();
          $_line_med->countPlanifications();
          if ($show_initiales_pharma) {
            $_line_med->loadRefUserValidationPharma();
          }
          if (in_array($_line_med->jour_decalage, array("ER", "R"))) {
            $_line_med->loadRefOperation();
          }
          if ($_line_med->delay_prise) {
            $_line_med->loadRefLastAdministration();
          }
          $_line_med->updateAlerteAntibio();
          $_line_med->loadActiveDates();

          if (CAppUI::conf("dPprescription CPrescription group_perf_atc", $group)&& CAppUI::pref("regroupement_med_plan_soins")) {
            $atc = $_line_med->_ref_produit->$_ref_name;
            if (!isset($atc_classes[$atc])) {
              $medicament_produit = new CMedicamentProduit();
              $atc_classes[$atc] = $medicament_produit->getLibelleATC($atc);
            }
          }
        }

        // Chargement des prescription_line_mixes
        $prescription->loadRefsPrescriptionLineMixes("", "1", 1, '', $hide_old_lines, null, null, $hide_line_inactive);
        foreach ($prescription->_ref_prescription_line_mixes as $_prescription_line_mix) {
          $_prescription_line_mix->countVariantes();
          $_prescription_line_mix->loadRefsVariantes();
          $_prescription_line_mix->getRecentModification();
          $_prescription_line_mix->loadRefsLines();
          $_prescription_line_mix->loadVoies();
          $_prescription_line_mix->loadRefPraticien();
          $_prescription_line_mix->loadRefLogSignaturePrat();
          $_prescription_line_mix->calculVariations();
          $_prescription_line_mix->loadRefsPrises();
          $_prescription_line_mix->countPlanifications();
          if (in_array($_prescription_line_mix->jour_decalage, array("ER", "R"))) {
            $_prescription_line_mix->loadRefOperation();
          }
          if ($show_initiales_pharma) {
            $_prescription_line_mix->loadRefUserValidationPharma();
          }
          $_prescription_line_mix->updateAlerteAntibio();
          $_prescription_line_mix->loadActiveDates();

          if ($_prescription_line_mix->sans_planif) {
            foreach ($_prescription_line_mix->_ref_lines as $_line_mix_item) {
              $_line_mix_item->loadRefLastAdministration();
            }
          }
          if (CAppUI::conf("dPprescription CPrescription group_perf_atc", $group) && CAppUI::pref("regroupement_med_plan_soins")) {
            $atcs = array();
            foreach ($_prescription_line_mix->loadRefsLines() as $_line_mix_item) {
              if (!$_line_mix_item->solvant && $_line_mix_item->atc) {
                $display_level = $prescription->_display_level ? $prescription->_display_level : '2';
                $_ref_name = "_ref_ATC_".$display_level."_code";
                $atc = $_line_mix_item->_ref_produit->$_ref_name;
                $atcs[] = $atc;
              }
            }
            if (count($atcs) == 1) {
              $_atc = reset($atcs);
              if (!isset($atc_classes[$_atc])) {
                $medicament_produit = new CMedicamentProduit();
                $atc_classes[$_atc] = $medicament_produit->getLibelleATC($_atc);
              }
            }
          }
        }
        // Chargement des lignes d'éléments
        if ($chapitre == "all_chaps") {
          $prescription->loadRefsLinesElementByCat("1", "1", null, null, null, null, $hide_old_lines, null, null, $hide_line_inactive);
          foreach ($prescription->_ref_prescription_lines_element as $_line_elt) {
            $_line_elt->countPlanifications();
            $_line_elt->loadActiveDates();
          }
        }
        elseif ($chapitre == "all_med" && CAppUI::pref("regroupement_med_plan_soins") && CAppUI::conf("soins suivi group_hors_amm_med", $group)) {
          $prescription->loadRefsLinesElementByCat("1", "1", "med_elt", null, null, null, $hide_old_lines, null, null, $hide_line_inactive);
          foreach ($prescription->_ref_prescription_lines_element as $_line_elt) {
            $_line_elt->countPlanifications();
            $_line_elt->loadActiveDates();
          }
        }
      }
      elseif ($chapitre == "med" || $chapitre == "inj") {
        $prescription->loadRefsLinesMedByCat("1", "1", '', $hide_old_lines, null, null, $hide_line_inactive);
        foreach ($prescription->_ref_prescription_lines as $_line_med) {
          $_line_med->loadRefLogSignee();
          if ($show_initiales_pharma) {
            $_line_med->loadRefUserValidationPharma();
          }
          $_line_med->countVariantes();
          $_line_med->countBackRefs("administration");
          $_line_med->loadRefsVariantes();
          $_line_med->countPlanifications();
          if ($_line_med->delay_prise) {
            $_line_med->loadRefLastAdministration();
          }
          $_line_med->updateAlerteAntibio();
          if (in_array($_line_med->jour_decalage, array("ER", "R"))) {
            $_line_med->loadRefOperation();
          }
          $_line_med->loadActiveDates();
        }
      }
      elseif ($chapitre == "perfusion" || $chapitre == "aerosol" || $chapitre == "alimentation" || $chapitre == "oxygene" || $chapitre == "preparation") {
        // Chargement des prescription_line_mixes
        $prescription->loadRefsPrescriptionLineMixes($chapitre, "1", 1, '', $hide_old_lines, null, null, $hide_line_inactive);
        foreach ($prescription->_ref_prescription_line_mixes as $_prescription_line_mix) {
          $_prescription_line_mix->countVariantes();
          $_prescription_line_mix->loadRefsVariantes();
          $_prescription_line_mix->getRecentModification();
          $_prescription_line_mix->loadRefsLines();
          $_prescription_line_mix->loadVoies();
          $_prescription_line_mix->loadRefPraticien();
          $_prescription_line_mix->loadRefLogSignaturePrat();
          $_prescription_line_mix->calculVariations();
          $_prescription_line_mix->countPlanifications();
          $_prescription_line_mix->loadRefsPrises();
          if (in_array($_prescription_line_mix->jour_decalage, array("ER", "R"))) {
            $_prescription_line_mix->loadRefOperation();
          }
          if ($show_initiales_pharma) {
            $_prescription_line_mix->loadRefUserValidationPharma();
          }
          $_prescription_line_mix->updateAlerteAntibio();
          $_prescription_line_mix->loadActiveDates();

          if ($_prescription_line_mix->sans_planif) {
            foreach ($_prescription_line_mix->_ref_lines as $_line_mix_item) {
              $_line_mix_item->loadRefLastAdministration();
            }
          }
        }
      }
      elseif ($chapitre == "inscription") {
        // Chargement des inscriptions effectuées
        $prescription->loadRefsLinesInscriptions();
        foreach ($prescription->_ref_lines_inscriptions as $_inscriptions_by_type) {
          foreach ($_inscriptions_by_type as &$_inscription) {
            $_inscription->countBackRefs("administration");
            $_inscription->loadRefLogSignee();
            $_inscription->countPlanifications();
            $_inscription->loadRefParentLine();
          }
        }
      }
      elseif (!$chapitre) {
        // Parcours initial pour afficher les onglets utiles (pas de chapitre de specifié)
        $prescription->loadRefsPrescriptionLineMixes("", "1");
        $prescription->loadRefsLinesMedByCat("1", "1");

        // Chargement des lignes d'elements
        $prescription->loadRefsLinesElementByCat("1", "1", null);

        if ($hide_old_lines) {
          $first_date = reset($_dates);
          if (count($prescription->_ref_prescription_line_mixes)) {
            foreach ($prescription->_ref_prescription_line_mixes as $key_line_mix => $_line_mix) {
              if ($_line_mix->_fin && $_line_mix->_fin < CMbDT::dateTime() && $_line_mix->_fin >= $first_date) {
                unset($prescription->_ref_prescription_line_mixes[$key_line_mix]);
                if (!$_line_mix->next_line_id || $_line_mix->countAdministrations()) {
                  $hidden_lines_count++;
                }
              }
            }
          }
          if (count($prescription->_ref_prescription_lines)) {
            foreach ($prescription->_ref_prescription_lines as $_key_line_med => $_line_med) {
              if ($_line_med->_fin_reelle && $_line_med->_fin_reelle < CMbDT::dateTime() && $_line_med->_fin_reelle >= $first_date) {
                unset($prescription->_ref_prescription_lines[$_key_line_med]);
                if (!$_line_med->child_id || $_line_med->countAdministrations()) {
                  $hidden_lines_count++;
                }
              }
            }
          }
          if (count($prescription->_ref_prescription_lines_element)) {
            foreach ($prescription->_ref_prescription_lines_element as $_key_line => $_line_elt) {
              if ($_line_elt->_fin_reelle && $_line_elt->_fin_reelle < CMbDT::dateTime() && $_line_elt->_fin_reelle >= $first_date) {
                unset($prescription->_ref_prescription_lines_element[$_key_line]);
                $category = $_line_elt->_ref_element_prescription->_ref_category_prescription;
                unset($prescription->_ref_prescription_lines_element_by_chap[$category->chapitre][$_line_elt->_id]);
                unset($prescription->_ref_prescription_lines_element_by_cat[$category->chapitre][$category->_id]["element"][$_line_elt->_id]);
                if (!$_line_elt->child_id || $_line_elt->countAdministrations()) {
                  $hidden_lines_count++;
                }
              }
            }
          }
        }

        if ($hide_line_inactive) {
          $date_time = CMbDT::date()." 00:00:00";
          $first_date = CMbDT::date();

          if (count($prescription->_ref_prescription_line_mixes)) {
            foreach ($prescription->_ref_prescription_line_mixes as $key_line_mix => $_line_mix) {
              $_line_mix->loadActiveDates();
              if (($_line_mix->conditionnel && (!$_line_mix->debut_activation || ($_line_mix->fin_activation && $_line_mix->fin_activation <= $date_time))
                && isset($_line_mix->_active_dates[$first_date])) || ($_line_mix->sans_planif && !$_line_mix->countAdministrations())) {
                unset($prescription->_ref_prescription_line_mixes[$key_line_mix]);
                if (!$_line_mix->next_line_id || $_line_mix->countAdministrations()) {
                  $hide_inactive_count++;
                }
              }
            }
          }
          if (count($prescription->_ref_prescription_lines)) {
            foreach ($prescription->_ref_prescription_lines as $_key_line_med => $_line_med) {
              $_line_med->loadActiveDates();
              if (($_line_med->conditionnel && (!$_line_med->debut_activation || ($_line_med->fin_activation && $_line_med->fin_activation <= $date_time))
                && isset($_line_med->_active_dates[$first_date])) || ($_line_med->sans_planif && !$_line_med->countAdministrations())) {
                unset($prescription->_ref_prescription_lines[$_key_line_med]);
                if (!$_line_med->child_id || $_line_med->countAdministrations()) {
                  $hide_inactive_count++;
                }
              }
            }
          }
          if (count($prescription->_ref_prescription_lines_element)) {
            foreach ($prescription->_ref_prescription_lines_element as $_key_line => $_line_elt) {
              $_line_elt->loadActiveDates();
              if (($_line_elt->conditionnel && (!$_line_elt->debut_activation || ($_line_elt->fin_activation && $_line_elt->fin_activation <= $date_time))
                && isset($_line_elt->_active_dates[$first_date])) || ($_line_elt->sans_planif && !$_line_elt->countAdministrations())) {
                unset($prescription->_ref_prescription_lines_element[$_key_line]);
                $category = $_line_elt->_ref_element_prescription->_ref_category_prescription;
                unset($prescription->_ref_prescription_lines_element_by_chap[$category->chapitre][$_line_elt->_id]);
                unset($prescription->_ref_prescription_lines_element_by_cat[$category->chapitre][$category->_id]["element"][$_line_elt->_id]);
                if (!$_line_elt->child_id || $_line_elt->countAdministrations()) {
                  $hide_inactive_count++;
                }
              }
            }
          }
        }
        if (@!CAppUI::conf("object_handlers CPrescriptionAlerteHandler")) {
          // Calcul des modifications recentes par chapitre
          $prescription->countRecentModif();
          $prescription->countUrgence($date);
        }
      }
      else {
        // Chargement des lignes d'elements  avec pour chapitre $chapitre
        $prescription->loadRefsLinesElementByCat("1", "1", $chapitre, null, null, null, $hide_old_lines, null, null, $hide_line_inactive);
        foreach ($prescription->_ref_prescription_lines_element as $_line_elt) {
          $_line_elt->countPlanifications();
          $_line_elt->loadActiveDates();
        }
      }

      $with_calcul = $chapitre ? true : false;

      $prescription->calculPlanSoin($_dates, 0, null, null, null, $with_calcul, "");

      // Chargement des operations
      if ($prescription->_ref_object instanceof CSejour) {
        $operation = new COperation();
        $operation->sejour_id = $prescription->object_id;
        $operation->annulee = "0";
        $_operations  = $operation->loadMatchingList();

        foreach ($_operations as $_operation) {
          if ($_operation->time_operation != "00:00:00") {
            $_operation->loadRefPlageop();
            $date_operation = CMbDT::date($_operation->_datetime_best);
            $hour_op = CMbDT::time($_operation->_datetime_best);
            $hour_operation = CMbDT::format($hour_op, '%H:00:00');
            $operations["$date_operation $hour_operation"] = $hour_op;
            $operations["$date_operation $hour_operation object"] = $_operation;
          }
        }
      }
    }
    // Calcul du nombre de produits (rowspan)
    $prescription->calculNbProduit();
    // Chargement des transmissions qui ciblent les lignes de la prescription
    $prescription->loadAllTransmissions();
  }

  if ($regroup_lines == "") {
    $regroup_lines = CAppUI::pref("regroup_lines_".$prescription->_ref_object->type);
  }
}

if ($chapitre) {
  if (count($prescription->_ref_prescription_line_mixes)) {
    foreach ($prescription->_ref_prescription_line_mixes as $_line_mix) {
      $_line_mix->loadRefParentLine();
    }
  }
  if (count($prescription->_ref_prescription_lines)) {
    foreach ($prescription->_ref_prescription_lines as $_line_med) {
      $_line_med->loadRefParentLine();
    }
  }
  if (count($prescription->_ref_prescription_lines_element)) {
    foreach ($prescription->_ref_prescription_lines_element as $_line_elt) {
      $_line_elt->loadRefParentLine();
    }
  }
}
$signe_decalage = ($nb_decalage < 0) ? "-" : "+";

$prolongation_time = CAppUI::conf("dPprescription general prolongation_time", $group->_guid);
$sortie_sejour = ($sejour->sortie_reelle || !$prolongation_time) ? $sejour->sortie : CMbDT::dateTime("+ $prolongation_time HOURS", $sejour->sortie);

$count_perop_adm = 0;
if (CAppUI::conf("dPprescription general show_perop_suivi_soins", $group->_guid) && $prescription->_id && !$chapitre) {
  $count_perop_adm = CAdministration::countPerop($prescription->_id);
}

if (!$chapitre && CAppUI::pref("regroupement_med_plan_soins") && CAppUI::conf("soins suivi group_hors_amm_med", $group) && isset($prescription->_ref_lines_elt_for_plan["med_elt"])) {
  foreach ($prescription->_ref_lines_elt_for_plan["med_elt"] as $_line_elt) {
    $prescription->_nb_lines_plan_soins["all_med"] ++;
  }
  unset($prescription->_ref_lines_elt_for_plan["med_elt"]);
}

/**
 * Tri par classe ATC
 *
 * @param string $atc1 ATC 1
 * @param string $atc2 ATC 2
 *
 * @return int
 */
function compareATC($atc1, $atc2) {
  global $atc_classes;
  return strcmp($atc_classes[$atc1], $atc_classes[$atc2]);
}

if (CAppUI::conf("dPprescription CPrescription group_perf_atc", $group) && CAppUI::pref("regroupement_med_plan_soins") && $prescription->object_id && count($prescription->_ref_all_med_for_plan) && $chapitre) {
  uksort($prescription->_ref_all_med_for_plan, "compareATC");
  if (isset($prescription->_ref_all_med_for_plan["other_perf"])) {
    $other = $prescription->_ref_all_med_for_plan["other_perf"];
    unset($prescription->_ref_all_med_for_plan["other_perf"]);
    $prescription->_ref_all_med_for_plan["other_perf"] = $other;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("risques_cis"         , $risques_cis);
$smarty->assign("plan_soins_unite_prescription", CAppUI::conf("dPprescription CPrescription unite_prescription_plan_soins", $group));
$smarty->assign("sortie_sejour"       , $sortie_sejour);
$smarty->assign("signe_decalage"      , $signe_decalage);
$smarty->assign("nb_decalage"         , abs($nb_decalage));
$smarty->assign("poids"               , $poids);
$smarty->assign("patient"             , $patient);
$smarty->assign("count_perop_adm"     , $count_perop_adm);
$smarty->assign("group_guid"          , $group->_guid);
$smarty->assign("atc_classes"         , $atc_classes);

if (CModule::getActive("dPprescription")) {
  $smarty->assign("prescription"        , $prescription);
  $smarty->assign("tabHours"            , $tabHours);
  $smarty->assign("prescription_id"     , $prescription->_id);
  $smarty->assign("categorie"           , new CCategoryPrescription());
  $smarty->assign("count_composition_dossier", $count_composition_dossier);
  $smarty->assign("composition_dossier" , $composition_dossier);
  $smarty->assign("bornes_composition_dossier", $bornes_composition_dossier);
  $smarty->assign("configs"             , $configs);
}

$smarty->assign("sejour"              , $sejour);
$smarty->assign("date"                , $date);
$smarty->assign("now"                 , CMbDT::dateTime());
$smarty->assign("real_date"           , CMbDT::date());
$smarty->assign("real_time"           , CMbDT::time());
$smarty->assign("operations"          , $operations);
$smarty->assign("mode_dossier"        , $mode_dossier);

$smarty->assign("prev_date"           , CMbDT::date("- 1 DAY", $date));
$smarty->assign("next_date"           , CMbDT::date("+ 1 DAY", $date));
$smarty->assign("today"               , CMbDT::date());
$smarty->assign("move_dossier_soin"   , false);
$smarty->assign("params"              , CConstantesMedicales::$list_constantes);
$smarty->assign("hide_close"          , $hide_close);
$smarty->assign("manual_planif"       , $planif_manuelle);
$smarty->assign("regroup_lines"       , $regroup_lines);
$smarty->assign("hide_old_lines"      , $hide_old_lines);
$smarty->assign("hidden_lines_count"  , $hidden_lines_count);
$smarty->assign("hide_line_inactive"  , $hide_line_inactive);
$smarty->assign("hide_inactive_count" , $hide_inactive_count);

// Affichage d'une ligne
if ($object_id && $object_class) {
  $smarty->assign("move_dossier_soin", true);
  $smarty->assign("nodebug", true);
  if ($line->_class == "CPrescriptionLineMix") {
    $smarty->assign("_prescription_line_mix", $line);
    $smarty->assign("with_navigation", $with_navigation);
    $smarty->display("../../dPprescription/templates/inc_vw_perf_dossier_soin.tpl");
  }
  else {
    if ($line->_class == "CPrescriptionLineElement") {
      $smarty->assign("name_cat", $name_cat);
      $smarty->assign("name_chap", $name_chap);
    }
    $smarty->assign("line", $line);
    $smarty->assign("line_id", $line->_id);
    $smarty->assign("line_class", $line->_class);
    $smarty->assign("transmissions_line", $line->_transmissions);
    $smarty->assign("administrations_line", $line->_administrations);
    $smarty->assign("unite_prise", $unite_prise);

    $smarty->display("../../dPprescription/templates/inc_vw_content_line_dossier_soin.tpl");
  }
}
else {
  // Affichage d'un chapitre
  if ($chapitre) {
    $smarty->assign("move_dossier_soin", false);
    $smarty->assign("chapitre", $chapitre);
    $smarty->assign("nodebug", true);

    $smarty->display("../../dPprescription/templates/inc_chapitre_dossier_soin.tpl");
  }
  else {
    // Affichage du plan de soin complet
    if (CModule::getActive("dPprescription")) {
      // Multiple prescriptions existante pour le séjour (Fusion des prescriptions)
      $prescription_multiple = new CPrescription;
      $where = array(
        "type" => " = 'sejour'",
        "object_class" => " = 'CSejour'",
        "object_id" => " = '$prescription->object_id'"
      );

      $multiple_prescription = $prescription_multiple->loadIds($where);
      $smarty->assign("multiple_prescription", $multiple_prescription);
    }

    $sejour->countTasks();

    $smarty->assign("admin_prescription", CModule::getCanDo("dPprescription")->admin || CMediusers::get()->isPraticien());
    $smarty->assign("move_dossier_soin"   , false);
    $smarty->display("inc_vw_dossier_soins.tpl");
  }
}
