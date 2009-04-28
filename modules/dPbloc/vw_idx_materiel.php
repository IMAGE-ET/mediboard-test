<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI, $can, $m, $g;
$can->needsRead();

$now       = mbDate();

$filter = new CMateriel;
$filter->_date_min = mbGetValueFromGet("_date_min"    , "$now");
$filter->_date_max = mbGetValueFromGet("_date_max"    , "$now");

$typeAff = mbGetValueFromGetOrSession("typeAff");

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where = array();

$salle = new CSalle();
$whereSalle["bloc_id"] = CSQLDataSource::prepareIn(array_keys(CGroups::loadCurrent()->loadBlocs(PERM_READ)));
$where["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)));

$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where["commande_mat"] = $typeAff ? "= '1'" : "!= '1'";
$where["annulee"]      = $typeAff ? "= '1'" : "!= '1'";

$order = "plagesop.date, rank";

$op = new COperation;
$op = $op->loadList($where, $order, null, null, $ljoin);
foreach($op as $key => $value) {
  $op[$key]->loadRefsFwd();
  $op[$key]->_ref_sejour->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("typeAff"	, $typeAff);
$smarty->assign("filter"    , $filter);
$smarty->assign("op"     	, $op);

$smarty->display("vw_idx_materiel.tpl");

?>