<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$date         = CValue::getOrSession("date", mbDate());
$bloc_id      = CValue::getOrSession("bloc_id");
$op_reveil_id = CValue::getOrSession("op_reveil_id");

$salle = new CSalle();
$where = array("bloc_id" => "= '$bloc_id'");
$order = "nom";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);
$interv = new COperation();
$plage = new CPlageOp();

foreach($listSalles as &$_salle) {
  // Recuperation des interventions
  $where = array();
  $where["operations.salle_id"] = "= '".$_salle->_id."'";
  $leftjoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
  $where["plagesop.date"] = " = '$date'";
  $where["entree_reveil"] = "IS NOT NULL";
  $where["sortie_reveil"] = "IS NULL";
  $order = "entree_reveil";
  $_salle->_list_patients_reveil = $interv->loadList($where, $order, null, null, $leftjoin);
  foreach($_salle->_list_patients_reveil as &$_interv) {
    $_interv->loadRefsFwd();
    $_interv->_presence_reveil = mbSubTime($_interv->entree_reveil, mbTime());
  }
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listSalles"  , $listSalles);
$smarty->assign("op_reveil_id", $op_reveil_id);

$smarty->display("inc_list_patients_reveil.tpl");

?>