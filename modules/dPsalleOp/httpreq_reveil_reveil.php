<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPsalleOp
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");

$date = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;

$timing = array();

// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

$listReveil = new COperation;
$where = array();
$where[] = "`plageop_id` ".$ds->prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NULL";
$order = "entree_reveil";
$listReveil = $listReveil->loadList($where, $order);
foreach($listReveil as $key => $value) {
  $listReveil[$key]->loadRefSejour();
  
  if($listReveil[$key]->_ref_sejour->type == "exte"){
    unset($listReveil[$key]);
    continue;
  }
  
  $listReveil[$key]->loadRefChir();
  $listReveil[$key]->loadRefPatient();
  $listReveil[$key]->loadAffectationsPersonnel();
  //_ref_affectation_reveil permet de stocker l'affectation qui a pour emplacement reveil
  $listReveil[$key]->_ref_affectation_reveil = new CAffectationPersonnel();
  
  if($listReveil[$key]->_ref_affectations_personnel["reveil"]){
    $listReveil[$key]->_ref_affectation_reveil = reset($listReveil[$key]->_ref_affectations_personnel["reveil"]);
  }

  $listReveil[$key]->_ref_sejour->loadRefsAffectations();
  if($listReveil[$key]->_ref_sejour->_ref_first_affectation->affectation_id) {
    $listReveil[$key]->_ref_sejour->_ref_first_affectation->loadRefLit();
    $listReveil[$key]->_ref_sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
  }
  
  $listReveil[$key]->loadRefPlageOp();
  
  //Tableau des timings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $value->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("$i minutes", $value->$key2);
    }
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listReveil"             , $listReveil              );
$smarty->assign("timing"                 , $timing                  );
$smarty->assign("date"                   , $date                    );
$smarty->assign("modif_operation"        , $modif_operation         );

$smarty->display("inc_reveil_reveil.tpl");
?>