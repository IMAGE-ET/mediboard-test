<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsRead();

$date    = CValue::getOrSession("date", mbDate());
$bloc_id = CValue::getOrSession("bloc_id");

$modif_operation = $date >= mbDate();
$hour = mbTime();

$timing = array();

// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

$salle = new CSalle();
$whereSalle = array("bloc_id" => " = '$bloc_id'");

$where = array();
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)));
$where[] = "plageop_id ".CSQLDataSource::prepareIn(array_keys($plages))." OR (plageop_id IS NULL AND date = '$date')";
$where["sortie_salle"] = "IS NOT NULL";
$where["entree_reveil"] = "IS NULL";
$where["sortie_reveil"] = "IS NULL";
$order = "sortie_salle";

$operation = new COperation;
$listOperations = $operation->loadList($where, $order);

foreach($listOperations as $key => &$op) {
  $op->loadRefSejour(1);

  if($op->_ref_sejour->type == "exte"){
    unset($listOperations[$key]);
    continue;
  }
  
  $op->loadRefChir(1);
  $op->loadRefPlageOp(1);
  $op->_ref_sejour->loadRefPatient(1);
  $op->loadAffectationsPersonnel();
  
  //Tableau des timings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -10; $i < 10 && $op->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("$i minutes", $op->$key2);
    }
  }
  if (CModule::getActive("bloodSalvage")) {
    $op->blood_salvage = new CBloodSalvage;
    $op->blood_salvage->operation_id = $key;
    $op->blood_salvage->loadMatchingObject();
    $op->blood_salvage->loadRefPlageOp();
    $op->blood_salvage->totaltime = "00:00:00";
    if($op->blood_salvage->recuperation_start && $op->blood_salvage->transfusion_end) {
      $op->blood_salvage->totaltime = mbTimeRelative($op->blood_salvage->recuperation_start, $op->blood_salvage->transfusion_end);
    } elseif($op->blood_salvage->recuperation_start){
      $op->blood_salvage->totaltime = mbTimeRelative($op->blood_salvage->recuperation_start,mbDate($op->blood_salvage->_datetime)." ".mbTime());
    }
  }
}

// Chargement de la liste du personnel pour le reveil
$personnels = array();
if(Cmodule::getActive("dPpersonnel")) {
  $personnel  = new CPersonnel();
  $personnels = $personnel->loadListPers("reveil");
}

// Vérification de la check list journalière
$check_list = CDailyCheckList::getTodaysList('CBlocOperatoire', $bloc_id, $date);
$check_list->loadItemTypes();
$check_list->loadBackRefs('items');

$where = array('target_class' => "= 'CBlocOperatoire'");
$check_item_category = new CDailyCheckItemCategory;
$check_item_categories = $check_item_category->loadList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("personnels"             , $personnels);
$smarty->assign("plages"                 , $plages);
$smarty->assign("listOps"                , $listOperations);
$smarty->assign("timing"                 , $timing);
$smarty->assign("date"                   , $date);
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("hour"                   , $hour);
$smarty->assign("modif_operation"        , $modif_operation);
$smarty->assign("check_list"             , $check_list);
$smarty->assign("check_item_categories"  , $check_item_categories);

$smarty->display("inc_reveil_ops.tpl");

?>