<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$services_ids = CValue::getOrSession("services_ids");
$readonly     = CValue::getOrSession("readonly");

$services_ids = CService::getServicesIdsPref($services_ids);

$smarty = new CSmartyDP();

$smarty->assign("readonly", $readonly);

$smarty->display("vw_placements.tpl");
