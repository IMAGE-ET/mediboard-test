<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$group_id    = CValue::get("group_id", CGroups::loadCurrent()->_id);
$type        = CValue::get("type");
$selected_id = CValue::get("selected_id");

$cpi = new CChargePriceIndicator;
$cpi->group_id = $group_id;
$cpi->type     = $type;
$cpi->actif    = 1;
$cpi_list = $cpi->loadMatchingList();

$smarty = new CSmartyDP();
$smarty->assign("cpi_list", $cpi_list);
$smarty->assign("selected_id", $selected_id);
$smarty->display("inc_list_cpi.tpl");
