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
$plage = new CPlageOp();
$where = array();
$where["date"] = "= '$date'";
$plages = $plage->loadList($where);

// Selection des salles du bloc
$salle = new CSalle();
$whereSalle = array("bloc_id" => " = '$bloc_id'");
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

// Récupération des interventions
$where = array();
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$where[] = "plageop_id ".CSQLDataSource::prepareIn(array_keys($plages))." OR (plageop_id IS NULL AND date = '$date')";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NULL";
$order = "entree_reveil";

$operation = new COperation();
$listOperations = $operation->loadList($where, $order);

foreach($listOperations as $key => &$op) {
  $op->loadRefSejour(1);
  
  if($op->_ref_sejour->type == "exte"){
    unset($listOperations[$key]);
    continue;
  }
  
  $op->loadRefChir(1);
  $op->loadRefPatient(1);
  $op->loadAffectationsPersonnel();
  
  if (CModule::getActive("bloodSalvage")) {
	  $op->blood_salvage= new CBloodSalvage;
	  $where = array();
	  $where["operation_id"] = "= '$key'";
	  $op->blood_salvage->loadObject($where);
	  $op->blood_salvage->loadRefPlageOp();
	  $op->blood_salvage->totaltime = "00:00:00";
    if($op->blood_salvage->recuperation_start && $op->blood_salvage->transfusion_end) {
      $op->blood_salvage->totaltime = mbTimeRelative($op->blood_salvage->recuperation_start, $op->blood_salvage->transfusion_end);
    } elseif($op->blood_salvage->recuperation_start){
      $op->blood_salvage->totaltime = mbTimeRelative($op->blood_salvage->recuperation_start,mbDate($op->blood_salvage->_datetime)." ".mbTime());
    }
  }

  $op->_ref_sejour->loadRefsAffectations();
  if($op->_ref_sejour->_ref_first_affectation->affectation_id) {
    $op->_ref_sejour->_ref_first_affectation->loadRefLit();
    $op->_ref_sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
  }
  
  $op->loadRefPlageOp(1);
  
  //Tableau des timings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $key2 !== null; $i++) {
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("personnels"             , $personnels);
$smarty->assign("listReveil"             , $listOperations);
$smarty->assign("timing"                 , $timing);
$smarty->assign("date"                   , $date);
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("modif_operation"        , $modif_operation);

$smarty->display("inc_reveil_reveil.tpl");

?>