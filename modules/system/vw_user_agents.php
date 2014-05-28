<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$ua = new CUserAgent();
/*$ds = $ua->getDS();

$request = new CRequest();
$request->addTable($ua->_spec->table);
$request->addSelect("DISTINCT `browser_name`, `platform_name`, `device_name`, `device_maker`, `device_type`, `pointing_method`");
$list = $ds->loadList($request->makeSelect());

$filter_names = array(
  "browser_name",
  "platform_name",
  "device_name",
  "device_maker",
  "device_type",
  "pointing_method",
);

$filter_values = array();
foreach ($filter_names as $_filter_name) {
  $_values = array_unique(CMbArray::pluck($list, $_filter_name));
  sort($_values);
  $filter_values[$_filter_name] = $_values;
}*/

$uas = $ua->loadList(null, "browser_name, browser_version", 100);

CStoredObject::massCountBackRefs($uas, "user_authentications");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user_agents", $uas);

$smarty->display("vw_user_agents.tpl");
