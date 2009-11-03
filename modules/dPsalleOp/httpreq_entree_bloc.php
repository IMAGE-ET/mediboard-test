<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$date = CValue::getOrSession("date", mbDate());
$hour = mbTime(null);
$date_now = mbDate();
$modif_operation = $date>=$date_now;

// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

$timing = array();

$listReveil = new COperation;
$where = array();
$where[] = "`plageop_id` ".CSQLDataSource::prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where[] = "((`sejour`.type = 'exte' AND `sortie_salle` IS NOT NULL) OR (`sejour`.type != 'exte' AND `entree_reveil` IS NOT NULL)) AND `sortie_reveil` IS NULL";

$ljoin = array();
$ljoin["sejour"] = "`sejour`.`sejour_id` = `operations`.`sejour_id`";
$order = "entree_reveil";
$listReveil = $listReveil->loadList($where, $order, null, null, $ljoin);
foreach($listReveil as $key => $value) {
  $listReveil[$key]->loadRefsFwd();
  $listReveil[$key]->_ref_sejour->loadRefsFwd();
  $listReveil[$key]->_ref_sejour->loadRefsAffectations();
  if($listReveil[$key]->_ref_sejour->_ref_first_affectation->affectation_id) {
    $listReveil[$key]->_ref_sejour->_ref_first_affectation->loadRefsFwd();
    $listReveil[$key]->_ref_sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
  }
  $listReveil[$key]->_ref_plageop->loadRefsFwd();
  //Tableau des timings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -10; $i < 10 && $value->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("$i minutes", $value->$key2);
    }
  }
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hour"           , $hour        );
$smarty->assign("listReveil"     , $listReveil  );
$smarty->assign("timing"         , $timing      );
$smarty->assign("date"           , $date        );
$smarty->assign("modif_operation", $modif_operation);

$smarty->display("inc_reveil_reveil.tpl");
?>