<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

global $m;

// On sauvegarde le module pour que les mises en session des paramètres se fassent
// dans le module depuis lequel on accède à la ressource
$save_m = $m;

$current_m = CValue::get("current_m");
$m         = $current_m;
$group = CGroups::loadCurrent();

$today           = CMbDT::date();
$date_planning   = CValue::getOrSession("date_planning", $today);
$praticien_id    = CValue::getOrSession("planning_chir_id");
$scroll_top      = CValue::get("scroll_top", null);
$bloc_id         = CValue::getOrSession("bloc_id");
$show_cancelled  = CValue::getOrSession("show_cancelled", 0);
$show_operations = CValue::getOrSession("show_operations", 1);

$days_limit_future = abs(CAppUI::pref("planning_resa_days_limit"));
$max_date_planning = CMbDT::date("+ $days_limit_future DAYS", $today);
if ($date_planning > $today && $days_limit_future != 0 && $date_planning > $max_date_planning) {
  $date_planning = $max_date_planning;
}

$days_limit_past = abs(CAppUI::pref("planning_resa_past_days_limit"));
$min_date_planning = CMbDT::date("- $days_limit_past DAYS", $today);
if ($date_planning < $today && $days_limit_past != 0 && $date_planning < $min_date_planning) {
  $date_planning = $min_date_planning;
}

CValue::setSession("date_planning", $date_planning);

//alerts
$nbIntervHorsPlage  = 0;
$nbIntervNonPlacees = 0;
$nbAlertesInterv    = 0;
$debut              = $fin = $date_planning;

$bloc  = new CBlocOperatoire();
$where = array();
if ($bloc_id) {
  $where["bloc_operatoire_id"] = " = '$bloc_id'";
}
$where["group_id"] = " = '$group->_id' ";
/** @var CBlocOperatoire[] $blocs */
$blocs = $bloc->loadList($where);
CStoredObject::filterByPerm($blocs, PERM_READ);

if (count($blocs) == 1) {
  $current_bloc = reset($blocs);
}

// optimisation du chargement des salles (one shot) + alertes
$salle = new CSalle();
$ds = $salle->getDS();
$where = array();
$where["bloc_id"] = $ds->prepareIn(array_keys($blocs));
$ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";
$order = "bloc_operatoire.nom, sallesbloc.nom";
$salles = $salle->loadList($where, $order, null, null, $ljoin);
$salles_ids = array_keys($salles);

$nbAlertesInterv = CBlocOperatoire::countAlertesIntervsForSalles(array_keys($salles));

foreach ($blocs as $_bloc) {
  $_bloc->canDo();
}


// Récupération des opérations
$operation = new COperation();

$where = array();
$ljoin = array();

$where["operations.date"] = "= '$date_planning'";
if (!$show_cancelled) {
  $where["operations.annulee"] = " != '1'";
}
//$where["operations.plageop_id"] = "IS NULL";
if ($bloc_id) {
  $ljoin["sallesbloc"]          = "sallesbloc.salle_id = operations.salle_id";
  $ljoin["bloc_operatoire"]     = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";
  $where["operations.salle_id"] = CSQLDataSource::prepareIn($salles_ids);
  $where["sallesbloc.bloc_id"]  = "= '$bloc_id'";
}

$praticien  = new CMediusers();
$praticiens = $praticien->loadPraticiens();

$where["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($praticiens), $praticien_id);
/** @var COperation[] $operations */
$operations = $operation->loadListWithPerms(PERM_READ, $where, null, null, "operations.operation_id", $ljoin);

$nbIntervHorsPlage = CIntervHorsPlage::countForDates($date_planning, null, array($praticien_id));

$prats = CStoredObject::massLoadFwdRef($operations, "chir_id");
CStoredObject::massLoadFwdRef($prats, "function_id");
CStoredObject::massLoadFwdRef($operations, "plageop_id");
CStoredObject::massLoadFwdRef($operations, "salle_id");
CStoredObject::massLoadFwdRef($operations, "anesth_id");
CStoredObject::massLoadFwdRef($operations, "chir_2_id");
CStoredObject::massLoadFwdRef($operations, "chir_3_id");
CStoredObject::massLoadFwdRef($operations, "chir_4_id");
CStoredObject::massLoadBackRefs($operations, "workflow");
$sejours = CStoredObject::massLoadFwdRef($operations, "sejour_id");
$affectations = CStoredObject::massLoadBackRefs($sejours, "affectations");
CStoredObject::massLoadFwdRef($affectations, "lit_id");
$patients = CStoredObject::massLoadFwdRef($sejours, "patient_id");
$dossiers = CStoredObject::massLoadBackRefs($patients, "dossier_medical");
CDossierMedical::massCountAllergies($dossiers);


// Récupération des commentaires
$commentaire = new CCommentairePlanning();
$where       = array();
$where["debut"]    = " <= '$date_planning 23:59:59'";
$where["fin"]      = " >= '$date_planning 00:00:00'";
$where["salle_id"] = CSQLDataSource::prepareIn($salles_ids);
$commentaires = $commentaire->loadListWithPerms(PERM_READ, $where);

// Récupération des plages opératoires
$plageop = new CPlageOp();

$where   = array();
$where["date"]     = " = '$date_planning'";
$where["salle_id"] = CSQLDataSource::prepareIn($salles_ids);

$plages = $plageop->loadListWithPerms(PERM_READ, $where);

// Création du planning
$planning        = new CPlanningWeek(0, 0, count($salles), count($salles), false, "auto");
$planning->title = "Planning du " . CMbDT::format($date_planning, CAppUI::conf("longdate"));


//load the current bloc
if (isset($current_bloc)) {
  $planning->title .= " - $current_bloc->nom";
}

$planning->guid         = "planning_interv";
$planning->hour_min     = str_pad(CAppUI::conf("reservation debut_planning"), 2, 0, STR_PAD_LEFT);
$planning->dragndrop    = $planning->resizable = CCanDo::edit() ? 1 : 0; //hack for "false => 0"
$planning->hour_divider = 12;
$planning->show_half    = true;
$i                      = 0;
$today                  = CMbDT::date();

foreach ($salles as $_salle) {
  $label_day = $bloc_id ? $_salle->_shortview : str_replace("-", "<br/>", $_salle->_view);
  $planning->addDayLabel($i, $label_day, null, null, null, true, array("salle_id" => $_salle->_id));

  if ($today == $date_planning) {
    $planning->addEvent(new CPlanningEvent(null, "$i " . CMbDT::time(), null, null, "red", null, "now"));
  }
  $i++;
}

// Tri des opérations par salle
$operations_by_salle = array();
foreach ($operations as $key => $_operation) {
  /** @var COperation $_operation */
  if (!$_operation->salle_id) {
    unset($operations[$key]);
    continue;
  }

  if (!isset($operations_by_salle[$_operation->salle_id])) {
    $operations_by_salle[$_operation->salle_id] = array();
  }

  // only hors plage
  if (!$_operation->plageop_id) {
    $operations_by_salle[$_operation->salle_id][$_operation->_id] = $_operation;
  }
}

// Tri des commentaires par salle
$commentaires_by_salle = array();
foreach ($commentaires as $key => $_commentaire) {
  /** @var CCommentairePlanning $_commentaire */
  $salle_id = $_commentaire->salle_id;
  if (!isset($commentaires_by_salle[$salle_id])) {
    $commentaires_by_salle[$salle_id] = array();
  }
  $commentaires_by_salle[$salle_id][] = $_commentaire;
}

// Tri des plages par salle
$plages_by_salle = array();
CMbObject::massLoadFwdRef($plages, "chir_id");
CMbObject::massLoadFwdRef($plages, "spec_id");

foreach ($plages as $_plage) {
  /** @var CPlageOp $_plage */
  $_plage->loadRefChir();
  $_plage->loadRefSpec();
  $salle_id = $_plage->salle_id;
  if (!isset($plages_by_salle[$salle_id])) {
    $plages_by_salle[$salle_id] = array();
  }
  $plages_by_salle[$salle_id][$_plage->_id] = $_plage;

  //load operation in salle (plage_id
  foreach ($operations as $_op) {

    if ($_op->plageop_id != $_plage->_id) {
      continue;
    }
    if ($praticien_id != $_op->chir_id && $praticien_id != "") {
      continue;
    }

    if (!$show_cancelled) {
      if (!$_op->annulee) {
        $operations_by_salle[$salle_id][$_op->_id] = $_op;
      }
    }
    else {
      $operations_by_salle[$salle_id][$_op->_id] = $_op;
    }
  }
}

// Ajout des événements (opérations)
$can_edit = CCanDo::edit();

$diff_hour_urgence = CAppUI::conf("reservation diff_hour_urgence");

//prestations
$prestations_journalieres = CPrestationJournaliere::loadCurrentList();
$prestation_id            = CAppUI::pref("prestation_id_hospi");

if ($show_operations) {
  /** @var $_operation COperation */
  foreach ($operations_by_salle as $salle_id => $_operations) {
    $i = array_search($salle_id, $salles_ids);
    foreach ($_operations as $_operation) {
      //CSQLDataSource::$trace = true;

      //en plage & non validé, skip
      if ($_operation->plageop_id && !$_operation->rank) {
        continue;
      }

      $_operation->_ref_salle = $salles[$_operation->salle_id];

      $workflow = $_operation->loadRefWorkflow();

      $sejour = $_operation->loadRefSejour();

      $chir   = $_operation->loadRefChir();
      $chir->loadRefFunction();
      $chir_2 = $_operation->loadRefChir2();
      $chir_2->loadRefFunction();
      $chir_3 = $_operation->loadRefChir3();
      $chir_3->loadRefFunction();
      $chir_4 = $_operation->loadRefChir4();
      $chir_4->loadRefFunction();
      $anesth = $_operation->loadRefAnesth();
      $anesth->loadRefFunction();

      $_operation->loadRefPlageOp();

      $affectation = $sejour->loadRefCurrAffectation($_operation->_datetime_best);
      //$affectation->loadRefLit(true);

      $_operation->_ref_affectation = $affectation;

      $lit  = $_operation->_ref_affectation->_ref_lit;

      $charge = $sejour->loadRefChargePriceIndicator();
      $patient = $sejour->loadRefPatient();
      $dossier_medical = $patient->loadRefDossierMedical(false);
      $dossier_medical->countAllergies();

      //antecedents
      $dossier_medical->countAntecedents(false);
      $count_atcd = $dossier_medical->_count_antecedents;

      //besoins
      $besoins = $_operation->loadRefsBesoins();
      if (count($besoins)) {
        CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
        foreach ($besoins as $_besoin) {
          $_besoin->loadRefTypeRessource();
        }
      }

      //liaisons
      $liaison_sejour = "";
      foreach ($sejour->loadAllLiaisonsForDay($date_planning) as $_liaison) {
        $liaison_sejour .= $_liaison;
        $liaison_sejour .= " | ";
      }

      $offset_bottom = 0;
      $offset_top    = 0;

      //best time (horaire voulu / time_operation)
      $horaire  = CMbDT::time($_operation->_datetime_best);
      $debut    = "$i {$horaire}";
      $debut_op = $horaire;
      $fin_op   = $_operation->sortie_salle ? $_operation->sortie_salle : CMbDT::addTime($_operation->temp_operation, $horaire);
      $duree    = CMbDT::minutesRelative($horaire, $fin_op);


      // pré op
      if ($_operation->presence_preop) {
        $hour_debut_preop = CMbDT::subTime($_operation->presence_preop, $_operation->time_operation);
        $offset_top       = CMbDT::minutesRelative($hour_debut_preop, $_operation->time_operation);
        $duree            = $duree + $offset_top;
        $debut            = "$i $hour_debut_preop";
      }

      //post op
      if ($_operation->presence_postop) {
        $hour_fin_postop = CMbDT::addTime($_operation->presence_postop, $fin_op);
        // Si l'heure de fin postop est inférieure à la fin de l'intervention, alors on est à la journée suivante
        // On simule une fin à 23h59 afin de rester dans la même journée
        $save_hour_fin_postop = "";
        if ($hour_fin_postop < $fin_op) {
          $save_hour_fin_postop = $hour_fin_postop;
          $hour_fin_postop = "23:59:59";
        }
        $offset_bottom   = CMbDT::minutesRelative($fin_op, $hour_fin_postop);
        if ($save_hour_fin_postop) {
          $offset_bottom += CMbDT::minutesRelative("00:00:00", $save_hour_fin_postop);
          // On simule une fin à 23h59, alors il faut encore une minute pour aller jusqu'à 00h00
          $offset_bottom += 1;
        }
        $duree           = $duree + $offset_bottom;
      }

      $datetime_creation = $workflow->date_creation ? $workflow->date_creation : $_operation->loadFirstLog()->date;
      $interv_en_urgence = abs(CMbDT::hoursRelative($_operation->_datetime_best, $datetime_creation)) <= $diff_hour_urgence;

      //factures
      $sejour->loadRefsFactureEtablissement();
      $facture = $sejour->_ref_last_facture;
      $_operation->loadLiaisonLibelle();

      //template de contenu
      $smarty = new CSmartyDP("modules/reservation");
      $smarty->assign("operation", $_operation);
      $smarty->assign("patient", $patient);
      $smarty->assign("sejour", $sejour);
      $smarty->assign("liaison_sejour", $liaison_sejour);
      $smarty->assign("charge", $charge);
      $smarty->assign("facture", $facture);
      $smarty->assign("debut_op", $debut_op);
      $smarty->assign("fin_op", $fin_op);
      $smarty->assign("lit", $lit);
      $smarty->assign("chir", $chir);
      $smarty->assign("chir_2", $chir_2);
      $smarty->assign("chir_3", $chir_3);
      $smarty->assign("chir_4", $chir_4);
      $smarty->assign("anesth", $anesth);
      $smarty->assign("count_atcd", $count_atcd);
      $smarty->assign("besoins", $besoins);
      $smarty->assign("interv_en_urgence", $interv_en_urgence);
      $smartyL = $smarty->fetch("inc_planning/libelle_plage.tpl");
      //@todo : UGLY, find a global better way !
      $smartyL = htmlspecialchars_decode(CMbString::htmlEntities($smartyL, ENT_NOQUOTES), ENT_NOQUOTES);

      // couleurs
      $color     = CAppUI::conf("hospi colors ".$sejour->type);
      $important = true;
      $css       = null;
      if ($charge->_id) {
        $color = $charge->color;
      }

      // font color
      if (CColorSpec::get_text_color($color) < 130) {
        $css .= "invert_color";
      }


      if (CAppUI::conf("dPplanningOp CSejour use_recuse") && $sejour->recuse == -1) {
        $css       .= "plage_recuse ";
      }

      if ($sejour->annule || $_operation->annulee) {
        $css       .= "hatching ";
        $important = false;
      }

      $event = new CPlanningEvent($_operation->_guid, $debut, $duree, $smartyL, "#$color", $important, $css, $_operation->_guid, false);

      if ($can_edit) {
        $event->addMenuItem("edit", "Modifier cette intervention");
        $event->addMenuItem("cut", "Couper cette intervention");
        $event->addMenuItem("copy", "Copier cette intervention");
        $event->addMenuItem("clock", utf8_encode("Modifier les dates d'entrée et sortie du séjour"));
      }

      if ($offset_bottom) {
        $event->offset_bottom      = $offset_bottom;
        $event->offset_bottom_text = "Post op";
      }
      //mbTrace($event->length);
      if ($offset_top) {
        $event->offset_top      = $offset_top;
        $event->offset_top_text = "Pre op";
      }

      $event->plage["id"] = $_operation->_id;
      $event->type        = "operation_horsplage";
      $event->draggable   = $event->resizable = CCanDo::edit();
      if ($_operation->rank) {
        $event->type      = "operation_enplage";
        $event->draggable = false;
      }

      $planning->addEvent($event);
    }
  }
}

// Ajout des événements (commentaires), OK
foreach ($commentaires_by_salle as $salle_id => $_commentaires) {
  $i = array_search($salle_id, $salles_ids);

  foreach ($_commentaires as $_commentaire) {
    $debut = "$i " . CMbDT::time($_commentaire->debut);

    $duree       = CMbDT::minutesRelative(CMbDT::time($_commentaire->debut), CMbDT::time($_commentaire->fin));
    $com_comm    = CMbString::htmlEntities($_commentaire->commentaire);
    $com_libelle = CMbString::htmlEntities($_commentaire->libelle);

    $libelle = "<span
    style=\"display: none;\"
    data-entree_prevue=\"$_commentaire->debut\"
    data-sortie_prevue=\"$_commentaire->fin\"
    data-libelle=\"$com_libelle\"
    data-commentaire=\"$com_comm\"
    data-duree=\"$duree\"
    data-color=\"$_commentaire->color\"></span>" .
      "<span style=\"font-size: 11px; font-weight: bold;\">" . $com_libelle . "</span>" .
      "\n<span class=\"compact\">" . $com_comm . "</span>";

    $event = new CPlanningEvent($_commentaire->_guid, $debut, $duree, $libelle, "#$_commentaire->color", true, null, $_commentaire->_guid, false);

    $event->type        = "commentaire_planning";
    $event->draggable   = $event->resizable = CCanDo::edit();
    $event->plage["id"] = $_commentaire->_id;

    if ($can_edit) {
      $event->addMenuItem("edit", "Modifier ce commentaire");
      $event->addMenuItem("copy", "Copier ce commentaire");
      $event->addMenuItem("cancel", "Supprimer ce commentaire");
    }

    $planning->addEvent($event);
  }
}

// Ajout des plages, OK
foreach ($plages_by_salle as $salle_id => $_plages) {
  $i = array_search($salle_id, $salles_ids);

  CMbObject::massLoadRefsNotes($_plages);
  CMbObject::massLoadFwdRef($_plages, "chir_id");
  CMbObject::massLoadFwdRef($_plages, "anesth_id");
  CMbObject::massLoadFwdRef($_plages, "spec_id");

  foreach ($_plages as $_plage) {
    $_plage->loadRefsNotes();

    $_plage->loadRefChir()->loadRefFunction();
    $_plage->loadRefSpec();

    $_plage->loadRefAnesth()->loadRefFunction();

    $debut = "$i " . CMbDT::time($_plage->debut);

    $duree = CMbDT::minutesRelative(CMbDT::time($_plage->debut), CMbDT::time($_plage->fin));

    //fetch
    $smarty = new CSmartyDP("modules/reservation");
    $smarty->assign("plageop", $_plage);
    $smarty_plageop = $smarty->fetch("inc_planning/libelle_plageop.tpl");
    $smarty_plageop = htmlspecialchars_decode(CMbString::htmlEntities($smarty_plageop, ENT_NOQUOTES), ENT_NOQUOTES);

    $event = new CPlanningEvent($_plage->_guid, $debut, $duree, $smarty_plageop, "#efbf99", true, null, $_plage->_guid, false);

    $event->below       = true;
    $event->type        = "plage_planning";
    $event->plage["id"] = $_plage->_id;

    if ($can_edit) {
      $event->addMenuItem("edit", CMbString::htmlEntities("Modifier cette plage"));
      $event->addMenuItem("list", CMbString::htmlEntities("Gestion des interventions"));
    }

    $planning->addEvent($event);
  }
}

$m = $save_m;

$planning->rearrange(true); //ReArrange the planning

$bank_holidays = CMbDate::getHolidays($date_planning);

$smarty = new CSmartyDP();

$smarty->assign("planning"            , $planning);
$smarty->assign("salles"              , $salles);
$smarty->assign("salles_ids"          , $salles_ids);
$smarty->assign("date_planning"       , $date_planning);
$smarty->assign("scroll_top"          , $scroll_top);
$smarty->assign("show_cancelled"      , $show_cancelled);
$smarty->assign("show_operations"     , $show_operations);
$smarty->assign("bank_holidays"       , $bank_holidays);
$smarty->assign("bloc_id"             , $bloc_id);
$smarty->assign("prestations"         , $prestations_journalieres);
$smarty->assign("height_planning_resa", CAppUI::pref("planning_resa_height", 1500));
$smarty->assign("nbIntervNonPlacees"  , $nbIntervNonPlacees);
$smarty->assign("nbIntervHorsPlage"   , $nbIntervHorsPlage);
$smarty->assign("nbAlertesInterv"     , $nbAlertesInterv);

$smarty->display("inc_vw_planning.tpl");
