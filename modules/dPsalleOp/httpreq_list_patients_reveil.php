<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$date         = mbGetValueFromGetOrSession("date", mbDate());
$bloc_id      = mbGetValueFromGetOrSession("bloc_id");
$op_reveil_id = mbGetValueFromGetOrSession("op_reveil_id");

$salle = new CSalle();
$where = array("bloc_id" => "= '$bloc_id'");
$order = "nom";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);
$interv = new COperation();
$plage = new CPlageOp();

foreach($listSalles as &$_salle) {
  // Selection des plages opératoires de la journée
  $where = array();
  $where["date"] = "= '$date'";
  $where["salle_id"] = "= '".$_salle->_id."'";
  $plages = $plage->loadList($where);
  
  // Récupération des interventions
  $where = array();
  $where["salle_id"] = "= '".$_salle->_id."'";
  $where[] = "plageop_id ".CSQLDataSource::prepareIn(array_keys($plages))." OR (plageop_id IS NULL AND date = '$date')";
  $where["entree_reveil"] = "IS NOT NULL";
  $where["sortie_reveil"] = "IS NULL";
  $order = "entree_reveil";
  $_salle->_list_patients_reveil = $interv->loadList($where, $order);
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