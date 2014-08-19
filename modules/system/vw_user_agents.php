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

/** @var CUserAgent[] $uas */
$uas = $ua->loadList(null, "browser_name, browser_version", 100);

CStoredObject::massCountBackRefs($uas, "user_authentications");

$browsers = array();
foreach ($uas as $_ua) {
  if (!isset($browsers[$_ua->browser_name])) {
    $browsers[$_ua->browser_name] = $_ua->_count["user_authentications"];
  }
  else {
    $browsers[$_ua->browser_name] += $_ua->_count["user_authentications"];
  }
}

$series = array(
  "title"   => CAppUI::tr("CUserAgent-browser_name"),
  "data"    => array(),
  "options" => array(
    "series" => array(
      "pie" => array(
        "show"  => true,
        "label" => array(
          "show"      => true,
          "threshold" => 0.02
        )
      )
    ),
    "legend" => array(
      "show" => false
    ),
    "colors" => array(
      "#33B1FF", "#CC9900", "#9999CC", "#FF66FF", "FFFF99", "#66CCFF", "#FF6666", "#009900",
      "#0066CC", "#996600", "#787878", "#66FF33", "#FF3300", "#00FF99", "#6666FF"
    ),
    "grid"   => array(
      "hoverable" => true
    )
  )
);

foreach ($browsers as $_browser => $_count) {
  $series["data"][] = array(
    "label" => $_browser,
    "data"  => $_count
  );
}
unset($browsers);

$smarty = new CSmartyDP();
$smarty->assign("user_agents", $uas);
$smarty->assign("graph", $series);
$smarty->display("vw_user_agents.tpl");
