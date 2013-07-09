<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$group_id    = CValue::get("group_id", CGroups::loadCurrent()->_id);
$type        = CValue::get("type");
$selected_id = CValue::get("selected_id");

$cpi = new CChargePriceIndicator;
$cpi->group_id = $group_id;
$cpi->actif    = 1;

if ($type) {
  $cpi->type = $type;
}

$cpi_list = $cpi->loadMatchingList("libelle");

$smarty = new CSmartyDP();
$smarty->assign("cpi_list", $cpi_list);
$smarty->assign("selected_id", $selected_id);
$smarty->display("inc_list_cpi.tpl");
