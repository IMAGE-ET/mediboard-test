<?php

/**
 * Vw_edit_planning
 *
 * @category Mediboard
 * @package  Bloc
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */





$date               = CValue::getOrSession("date", CMbDT::date());
$type_view_planning = CValue::getOrSession("type_view_planning", "day");

if ($type_view_planning == "day") {
  $debut = $fin = $date;
}
else {
  //sunday = first day of week ...
  if (date("w", strtotime($date)) == 0) {
    $date = CMbDT::date("-1 DAY", $date);
  }
  $debut = CMbDT::date("this week", $date);
  $fin   = CMbDT::date("next sunday", $debut);
}

// Liste des jours
$listDays = array();
for ($i = $debut; $i <= $fin; $i = CMbDT::date("+1 day", $i)) {
  $listDays[$i] = $i;  
}

$listBlocs  = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id    = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);
if (!array_key_exists($bloc_id, $listBlocs)) {
  $bloc_id = reset($listBlocs)->_id;
}
$listSalles = array();

/**
 * @var $curr_bloc CBlocOperatoire
 */
foreach ($listBlocs as &$curr_bloc) {
  $curr_bloc->loadRefsSalles();
}

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->canDo();
$bloc->loadRefsSalles();
$nbAlertesInterv = count($bloc->loadRefsAlertesIntervs());

if (!$listSalles = $bloc->_ref_salles) {
  $listSalles = array();
}

// Création du tableau de visualisation
$affichages = array();
foreach ($listDays as $keyDate => $valDate) {
  foreach ($listSalles as $keySalle => $valSalle) {
    $valSalle->_blocage[$valDate] = $valSalle->loadRefsBlocages($valDate);
    foreach (CPlageOp::$hours as $keyHours => $valHours) {
      foreach (CPlageOp::$minutes as $keyMins => $valMins) {
        // Initialisation du tableau
        $affichages["$keyDate-s$keySalle-$valHours:$valMins:00"] = "empty";
        $affichages["$keyDate-s$keySalle-HorsPlage"] = array();
      }
    }
  }
}

$listPlages         = array();
$operation          = new COperation();
$nbIntervHorsPlage  = 0;
$listPlage          = new CPlageOp();
$nbIntervNonPlacees = 0;

// Nombre d'interventions hors plage pour la semaine
$ljoin = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where = array();
$where["date"]            = "BETWEEN '$debut' AND '$fin'";
$where["annulee"]         = "= '0'";
$where[]                  = "salle_id IS NULL OR salle_id ". CSQLDataSource::prepareIn(array_keys($listSalles));
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$nbIntervHorsPlage = $operation->countList($where, null, $ljoin);

foreach ($listDays as $keyDate => $valDate) {
  
  // Récupération des plages par jour
  $where = array();
  $where["date"]     = "= '$keyDate'";
  $where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
  $order             = "debut";
  $listPlages[$keyDate] = $listPlage->loadList($where, $order);
  
  // Récupération des interventions hors plages du jour
  $where = array();
  $where["date"]     = "= '$keyDate'";
  $where["annulee"]   = "= '0'";
  $where["salle_id"]        = CSQLDataSource::prepareIn(array_keys($listSalles));
  $order = "time_operation";
  $horsPlages = $operation->loadList($where, $order);
  
  // Détermination des bornes du semainier
  $min = CPlageOp::$hours_start.":".reset(CPlageOp::$minutes).":00";
  $max = CPlageOp::$hours_stop.":".end(CPlageOp::$minutes).":00";

  /**
   * @var $plage CplageOp
   */
  // Détermination des bornes de chaque plage
  foreach ($listPlages[$keyDate] as $plage) {
    $plage->loadRefsFwd();
    $plage->loadRefsNotes();
    $plage->_ref_chir->loadRefsFwd();
    $plage->getNbOperations();
    $nbIntervNonPlacees += $plage->_nb_operations - $plage->_nb_operations_placees;
    $plage->loadAffectationsPersonnel();
  
    $plage->fin = min($plage->fin, $max);
    $plage->debut = max($plage->debut, $min);
  
    $plage->updateFormFields();
    $plage->makeView();
  
    if ($plage->debut >= $plage->fin) {
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }
  
  // Remplissage du tableau de visualisation
  foreach ($listPlages[$keyDate] as $plage) {
    $plage->debut = CMbDT::timeGetNearestMinsWithInterval($plage->debut, CPlageOp::$minutes_interval);
    $plage->fin   = CMbDT::timeGetNearestMinsWithInterval($plage->fin  , CPlageOp::$minutes_interval);
    $plage->_nbQuartHeure = CMbDT::timeCountIntervals($plage->debut, $plage->fin, "00:".CPlageOp::$minutes_interval.":00");
    for ($time = $plage->debut; $time < $plage->fin; $time = CMbDT::time("+".CPlageOp::$minutes_interval." minutes", $time) ) {
      $affichages["$keyDate-s$plage->salle_id-$time"] = "full";
    } 
    $affichages["$keyDate-s$plage->salle_id-$plage->debut"] = $plage->_id;
  }
  // Ajout des interventions hors plage
  /**
   * @var $_op COperation
   */
  foreach ($horsPlages as $_op) {
    if ($_op->salle_id) {
      $affichages["$keyDate-s".$_op->salle_id."-HorsPlage"][$_op->_id] = $_op;
    }
  }
}

// Liste des Spécialités
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();

//Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPlages"        , $listPlages        );
$smarty->assign("listDays"          , $listDays          );
$smarty->assign("listBlocs"         , $listBlocs         );
$smarty->assign("bloc"              , $bloc              );
$smarty->assign("listSalles"        , $listSalles        );
$smarty->assign("listHours"         , CPlageOp::$hours   );
$smarty->assign("listMins"          , CPlageOp::$minutes );
$smarty->assign("type_view_planning", $type_view_planning);
$smarty->assign("affichages"        , $affichages        );
$smarty->assign("nbIntervNonPlacees", $nbIntervNonPlacees);
$smarty->assign("nbIntervHorsPlage" , $nbIntervHorsPlage );
$smarty->assign("nbAlertesInterv"   , $nbAlertesInterv   );
$smarty->assign("date"              , $date              );
$smarty->assign("listSpec"          , $listSpec          );

$smarty->display("vw_edit_planning.tpl");
