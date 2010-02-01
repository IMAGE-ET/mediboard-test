<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$date    = CValue::getOrSession("date", mbDate());

$date = mbDate("last sunday", $date);
$fin  = mbDate("next sunday", $date);
$date = mbDate("+1 day", $date);

// Liste des jours
$listDays = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $date);
  $listDays[$dateArr] = $dateArr;  
}

// Liste des blocs
$listBlocs = CGroups::loadCurrent()->loadBlocs();
$bloc_id   = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();
$nbAlertesInterv = count($bloc->loadRefsAlertesIntervs());

if (!$listSalles = $bloc->_ref_salles) {
  $listSalles = array();
}

// Création du tableau de visualisation
$affichages = array();
foreach($listDays as $keyDate=>$valDate){
  foreach($listSalles as $keySalle=>$valSalle){
    foreach(CPlageOp::$hours as $keyHours=>$valHours){
      foreach(CPlageOp::$minutes as $keyMins=>$valMins){
        // Initialisation du tableau
        $affichages["$keyDate-s$keySalle-$valHours:$valMins:00"] = "empty";
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
$where["date"]            = "BETWEEN '$date' AND '$fin'";
$where[]                  = "salle_id IS NULL OR salle_id ". CSQLDataSource::prepareIn(array_keys($listSalles));
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$nbIntervHorsPlage = $operation->countList($where, null, null, null, $ljoin);

// Extraction des plagesOp par date
$where = array();
$where["date"]     = "BETWEEN '$date' AND '$fin'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$order             = "debut";

foreach($listDays as $keyDate => $valDate){
  
  // Récupération des plages par jour
  $where["date"] = "= '$keyDate'";
  $listPlages[$keyDate] = $listPlage->loadList($where,$order);
  
  // Détermination des bornes du semainier
  $min = CPlageOp::$hours_start.":".reset(CPlageOp::$minutes).":00";
  $max = CPlageOp::$hours_stop.":".end(CPlageOp::$minutes).":00";
  
  // Détermination des bornes de chaque plage
  foreach($listPlages[$keyDate] as $plage){
    $plage->loadRefsFwd();
    $plage->_ref_chir->loadRefsFwd();
    $plage->getNbOperations();
    $nbIntervNonPlacees += $plage->_nb_operations - $plage->_nb_operations_placees;
  
    $plage->fin = min($plage->fin, $max);
    $plage->debut = max($plage->debut, $min);
  
    $plage->updateFormFields();
    $plage->makeView();
  
    if($plage->debut >= $plage->fin){  
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }
  
  
  foreach($listPlages[$keyDate] as $plage){
    $plage->debut = mbTimeGetNearestMinsWithInterval($plage->debut, CPlageOp::$minutes_interval);
    $plage->fin   = mbTimeGetNearestMinsWithInterval($plage->fin  , CPlageOp::$minutes_interval);
    $plage->_nbQuartHeure = mbTimeCountIntervals($plage->debut, $plage->fin, "00:".CPlageOp::$minutes_interval.":00");
    for($time = $plage->debut; $time < $plage->fin; $time = mbTime("+".CPlageOp::$minutes_interval." minutes", $time) ){
      $affichages["$keyDate-s$plage->salle_id-$time"] = "full";
    } 
    $affichages["$keyDate-s$plage->salle_id-$plage->debut"] = $plage->_id;
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
$smarty->assign("affichages"        , $affichages        );
$smarty->assign("nbIntervNonPlacees", $nbIntervNonPlacees);
$smarty->assign("nbIntervHorsPlage" , $nbIntervHorsPlage );
$smarty->assign("nbAlertesInterv"   , $nbAlertesInterv   );
$smarty->assign("date"              , $date              );
$smarty->assign("listSpec"          , $listSpec          );

$smarty->display("vw_planning_week.tpl");
?>
