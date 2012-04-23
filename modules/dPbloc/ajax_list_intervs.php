<?php /* $Id: vw_edit_interventions.php 7678 2009-12-21 15:04:55Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: 7678 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$plageop_id = CValue::get("plageop_id");
$list_type  = CValue::get("list_type", "left");

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

// Infos sur la plage opératoire
$plage = new CPlageOp;
$plage->load($plageop_id);
$plage->loadRefsFwd();

$interv = new COperation();
$where = array();
$where["operations.plageop_id"] = "= '$plageop_id'";
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

if($list_type == "left") {
  $where["rank"] = "= '0'";
  $order = "operations.temp_operation";
} else {
  $where["rank"] = "!= '0'";
  $order = "operations.rank";
}
$intervs = $interv->loadList($where, $order, null, null, $ljoin);
foreach($intervs as $_interv) {
  $_interv->loadRefsFwd();
  $_interv->_ref_chir->loadRefFunction();
  $_interv->_ref_sejour->loadRefsFwd();
}

// liste des plages du praticien
$listPlages = new CPlageOp();
$listSalle = array();
$where = array();

$where["date"]    = "= '$plage->date'";
$where["chir_id"] = "= '$plage->chir_id'";
$listPlages = $listPlages->loadList($where);
foreach($listPlages as $keyPlages=>$valPlages){
  $listPlages[$keyPlages]->loadRefSalle();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listPlages", $listPlages);
$smarty->assign("plage"     , $plage);
$smarty->assign("anesth"    , $anesth);
$smarty->assign("intervs"   , $intervs);
$smarty->assign("list_type" , $list_type);
$smarty->display("inc_list_intervs.tpl");

?>