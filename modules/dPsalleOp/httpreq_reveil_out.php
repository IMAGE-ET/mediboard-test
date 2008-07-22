<?php

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");

$date = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;


// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

$timing = array();

$listOut = new COperation;
$where = array();
$where[] = "`plageop_id` ".$ds->prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NOT NULL";
$order = "sortie_reveil DESC";
$listOut = $listOut->loadList($where, $order);
foreach($listOut as $key => $value) {
  $listOut[$key]->loadRefSejour();
  
  if($listOut[$key]->_ref_sejour->type == "exte"){
    unset($listOut[$key]);
    continue;
  }
  $listOut[$key]->loadRefChir();
  $listOut[$key]->loadAffectationsPersonnel();
  $listOut[$key]->_ref_affectation_reveil = new CAffectationPersonnel();
  if($listOut[$key]->_ref_affectations_personnel["reveil"]){
    $listOut[$key]->_ref_affectation_reveil = reset($listOut[$key]->_ref_affectations_personnel["reveil"]);
  }

  $listOut[$key]->_ref_sejour->loadRefPatient();
  $listOut[$key]->_ref_sejour->loadRefsAffectations();
  if($listOut[$key]->_ref_sejour->_ref_first_affectation->affectation_id) {
    $listOut[$key]->_ref_sejour->_ref_first_affectation->loadRefLit();
    $listOut[$key]->_ref_sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
  }
  $listOut[$key]->loadRefPlageOp();
  //Tableau des timings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $value->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("$i minutes", $value->$key2);
    }
  }
}

$isbloodSalvageInstalled = CModule::getActive("bloodSalvage");

// Création du template
$smarty = new CSmartyDP();


$smarty->assign("listOut"                , $listOut                 );
$smarty->assign("isbloodSalvageInstalled", $isbloodSalvageInstalled );
$smarty->assign("timing"                 , $timing                  );
$smarty->assign("date"                   , $date                    );
$smarty->assign("modif_operation"        , $modif_operation         );

$smarty->display("inc_reveil_out.tpl");
?>