<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Alexis Granger
*/

global $can, $m, $g;

$can->needsRead();

$operation_id = CValue::getOrSession("operation_id");
$date  = CValue::getOrSession("date", mbDate());
$modif_operation = $date >= mbDate();


$operation = new COperation();
if($operation_id){
  $operation->load($operation_id);
  $operation->loadRefs();
  
  // Tableau des timings
  $timing["entree_salle"]    = array();
  $timing["pose_garrot"]     = array();
  $timing["debut_op"]        = array();
  $timing["fin_op"]          = array();
  $timing["retrait_garrot"]  = array();
  $timing["sortie_salle"]    = array();
  $timing["induction_debut"] = array();
  $timing["induction_fin"]   = array();
  foreach($timing as $key => $value) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $operation->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $operation->$key);
    }
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp",           $operation);
$smarty->assign("date",            $date);
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("timing",          $timing);

$smarty->display("inc_vw_timing.tpl");

?>