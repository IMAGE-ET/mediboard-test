<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$date       = CValue::getOrSession("date", mbDate());
$plageop_id = CValue::getOrSession("plageop_id");

$listBlocs  = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id    = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);
$listSalles = array();

foreach($listBlocs as &$curr_bloc) {
  $curr_bloc->loadRefsSalles();
}

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();

$listSalles = $bloc->_ref_salles;
  
// Informations sur la plage demandée
$plagesel = new CPlageOp;
$plagesel->load($plageop_id);
if($plagesel->_id){
  $arrKeySalle = array_keys($listSalles);
  if(!in_array($plagesel->salle_id, $arrKeySalle) || $plagesel->date != $date) {
    $plageop_id = 0;
    $plagesel = new CPlageOp;
  }
}

if(!$plagesel->_id) {
  $plagesel->debut = CPlageOp::$hours_start.":00:00";
  $plagesel->fin   = CPlageOp::$hours_start.":00:00";
}

// Liste des Specialités
$function = new CFunctions;
$specs = $function->loadSpecialites(PERM_READ);
foreach($specs as $key => $spec) {
  $specs[$key]->loadRefsUsers(array("Chirurgien", "Anesthésiste"));
}

// Liste des Anesthésistes
$mediuser = new CMediusers;
$anesths = $mediuser->loadAnesthesistes();

// Récupération des plages pour le jour demandé
$listPlage = new CPlageOp();
$where = array();
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$where["date"] = "= '$date'";
$order = "debut";
$listPlages[$date] = $listPlage->loadList($where,$order);

// Détermination des bornes du semainier
$min = CPlageOp::$hours_start.":".reset(CPlageOp::$minutes).":00";
$max = CPlageOp::$hours_stop.":".end(CPlageOp::$minutes).":00";

// Nombre d'interventions hors plage pour la journée
$operation = new COperation();
$ljoin = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where = array();
$where["operations.date"] = "= '$date'";
$where[]                  = "operations.salle_id IS NULL OR operations.salle_id ". CSQLDataSource::prepareIn(array_keys($listSalles));
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$nbIntervHorsPlage = $operation->countList($where, null, null, null, $ljoin);

$nbIntervNonPlacees = 0;
// Détermination des bornes de chaque plage
foreach($listPlages[$date] as &$plage){
  $plage->loadRefsFwd();
  $plage->_ref_chir->loadRefsFwd();
  $plage->getNbOperations();
  $nbIntervNonPlacees += $plage->_nb_operations - $plage->_nb_operations_placees;
  $plage->loadAffectationsPersonnel();
  
  $plage->fin = min($plage->fin, $max);
  $plage->debut = max($plage->debut, $min);
  
  $plage->updateFormFields();
  $plage->makeView();
  
  if($plage->debut >= $plage->fin){  
    unset($listPlages[$date][$plage->_id]);
  }  
}

// Création du tableau de visualisation vide
$affichages = array();
foreach($listSalles as $keySalle=>$valSalle){
  foreach(CPlageOp::$hours as $keyHours=>$valHours){
    foreach(CPlageOp::$minutes as $keyMins=>$valMins){
      // Initialisation du tableau
      $affichages["$date-s$keySalle-$valHours:$valMins:00"] = "empty";
    }
  }
}

// Remplissage du tableau de visualisation
foreach($listPlages[$date] as &$plage){
  $plage->debut = mbTimeGetNearestMinsWithInterval($plage->debut, CPlageOp::$minutes_interval);
  $plage->fin   = mbTimeGetNearestMinsWithInterval($plage->fin  , CPlageOp::$minutes_interval);
  $plage->_nbQuartHeure = mbTimeCountIntervals($plage->debut, $plage->fin, "00:".CPlageOp::$minutes_interval.":00");
  for($time = $plage->debut; $time < $plage->fin; $time = mbTime("+".CPlageOp::$minutes_interval." minutes", $time) ){
    $affichages["$date-s$plage->salle_id-$time"] = "full";
  } 
  $affichages["$date-s$plage->salle_id-$plage->debut"] = $plage->_id;
}

//Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPlages"        , $listPlages        );
$smarty->assign("bloc"              , $bloc              );
$smarty->assign("listBlocs"         , $listBlocs         );
$smarty->assign("listSalles"        , $listSalles        );
$smarty->assign("bloc"              , $bloc              );
$smarty->assign("listHours"         , CPlageOp::$hours   );
$smarty->assign("listMins"          , CPlageOp::$minutes );
$smarty->assign("affichages"        , $affichages        );
$smarty->assign("nbIntervNonPlacees", $nbIntervNonPlacees);
$smarty->assign("nbIntervHorsPlage" , $nbIntervHorsPlage );
$smarty->assign("date"              , $date              );
$smarty->assign("listSpec"          , $specs             );
$smarty->assign("plagesel"          , $plagesel          );
$smarty->assign("specs"             , $specs             );
$smarty->assign("anesths"           , $anesths           );

$smarty->display("vw_edit_planning.tpl");
?>
