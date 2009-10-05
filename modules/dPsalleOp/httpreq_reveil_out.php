<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPsalleOp
 *  @version $Revision$
 *  @author Alexandre Germonneau
 */

global $can;
$can->needsRead();

$date    = mbGetValueFromGetOrSession("date", mbDate());
$bloc_id = mbGetValueFromGetOrSession("bloc_id");

$modif_operation = $date >= mbDate();

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
$where["sortie_reveil"] = "IS NOT NULL";
$order = "sortie_reveil DESC";

$operation = new COperation;
$listOperations = $operation->loadList($where, $order);

foreach($listOperations as $key => &$op) {
  $op->loadRefSejour(1);
  
  if($op->_ref_sejour->type == "exte"){
    unset($listOperations[$key]);
    continue;
  }
  
  $op->loadRefChir(1);
  $op->loadAffectationsPersonnel();

  $op->_ref_sejour->loadRefPatient(1);
  $op->_ref_sejour->loadRefsAffectations();
  if($op->_ref_sejour->_ref_first_affectation->_id) {
    $op->_ref_sejour->_ref_first_affectation->loadRefLit();
    $op->_ref_sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
  }
  $op->loadRefPlageOp(1);
  
  //Tableau des timings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $op->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("$i minutes", $op->$key2);
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
$smarty->assign("listOut"                , $listOperations);
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("timing"                 , $timing);
$smarty->assign("date"                   , $date);
$smarty->assign("modif_operation"        , $modif_operation);
$smarty->assign("check_list"             , $check_list);
$smarty->assign("check_item_categories"  , $check_item_categories);

$smarty->display("inc_reveil_out.tpl");
?>