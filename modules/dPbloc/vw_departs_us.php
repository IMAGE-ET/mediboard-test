<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$bloc_id     = CValue::getOrSession("bloc_id");
$order_way   = CValue::getOrSession("order_way", "_heure_us");
$order_col   = CValue::getOrSession("order_col", "ASC");

$bloc = new CBlocOperatoire();
$where = array("group_id" => "= '".CGroups::loadCurrent()->_id."'");
$blocs = $bloc->loadListWithPerms(PERM_READ, $where, "nom");

$smarty = new CSmartyDP;

$smarty->assign("blocs"      , $blocs);
$smarty->assign("bloc_id"    , $bloc_id);
$smarty->assign("order_col"  , $order_col);
$smarty->assign("order_way"  , $order_way);

$smarty->display("vw_departs_us.tpl");
