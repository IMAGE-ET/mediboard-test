<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::read();

$listBlocs = CGroups::loadCurrent()->loadBlocs();
$listSalles = array();
foreach($listBlocs as $_bloc) {
  $listSalles = $listSalles + $_bloc->loadRefsSalles();
}

$salle = new CSalle();
$salle->load(CValue::get("salle_id"), reset($listSalles)->_id);

$date = CValue::get("date", mbDate());

// Liste des jours
$listDays = array();
for($i = 0; $i < 19*7; $i += 7) {
  $dateArr = mbDate("+$i day", $date);
  $listDays[$dateArr] = $dateArr;  
}

// Création du tableau de visualisation
$affichages = array();
foreach($listDays as $keyDate=>$valDate){
  foreach(CPlageOp::$hours as $keyHours=>$valHours){
    foreach(CPlageOp::$minutes as $keyMins=>$valMins){
      // Initialisation du tableau
      $affichages["$keyDate-$valHours:$valMins:00"] = "empty";
      $affichages["$keyDate-HorsPlage"] = array();
    }
  }
}

$listPlages         = array();
$operation          = new COperation();
$nbIntervHorsPlage  = 0;
$listPlage          = new CPlageOp();
$nbIntervNonPlacees = 0;

foreach($listDays as $keyDate => $valDate){
  
  // Récupération des plages par jour
  $where = array();
  $where["date"]     = "= '$keyDate'";
  $where["salle_id"] = "= '$salle->_id'";
  $order             = "debut";
  $listPlages[$keyDate] = $listPlage->loadList($where,$order);
  
  // Récupération des interventions hors plages du jour
  $where = array();
  $where["date"]      = "= '$keyDate'";
  $where["annulee"]   = "= '0'";
  $where["salle_id"]  = "= '$salle->_id'";
  $order = "time_operation";
  $horsPlages = $operation->loadList($where,$order);
  
  // Détermination des bornes du semainier
  $min = CPlageOp::$hours_start.":".reset(CPlageOp::$minutes).":00";
  $max = CPlageOp::$hours_stop.":".end(CPlageOp::$minutes).":00";
  
  // Détermination des bornes de chaque plage
  foreach($listPlages[$keyDate] as $plage){
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
  
    if($plage->debut >= $plage->fin){  
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }
  
  // Remplissage du tableau de visualisation
  foreach($listPlages[$keyDate] as $plage){
    $plage->debut = mbTimeGetNearestMinsWithInterval($plage->debut, CPlageOp::$minutes_interval);
    $plage->fin   = mbTimeGetNearestMinsWithInterval($plage->fin  , CPlageOp::$minutes_interval);
    $plage->_nbQuartHeure = mbTimeCountIntervals($plage->debut, $plage->fin, "00:".CPlageOp::$minutes_interval.":00");
    for($time = $plage->debut; $time < $plage->fin; $time = mbTime("+".CPlageOp::$minutes_interval." minutes", $time) ){
      $affichages["$keyDate-$time"] = "full";
    } 
    $affichages["$keyDate-$plage->debut"] = $plage->_id;
  }
  // Ajout des interventions hors plage
  foreach($horsPlages as $_op) {
    if($_op->salle_id) {
      $affichages["$keyDate-HorsPlage"][$_op->_id] = $_op;
    }
  }
}

//Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPlages"        , $listPlages        );
$smarty->assign("listDays"          , $listDays          );
$smarty->assign("listBlocs"         , $listBlocs         );
$smarty->assign("salle"             , $salle             );
$smarty->assign("listHours"         , CPlageOp::$hours   );
$smarty->assign("listMins"          , CPlageOp::$minutes );
$smarty->assign("affichages"        , $affichages        );
$smarty->assign("date"              , $date              );

$smarty->display("monitor_day_salle.tpl");

?>
