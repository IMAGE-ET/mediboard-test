<?php
/**
 * $Id: about.php 20799 2013-10-29 11:43:54Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20799 $
 */

CCanDo::checkRead();

$user = CAppUI::$user;
$latest_cache_key = "$user->_guid-latest_cache";
$latest_cache = SHM::get($latest_cache_key);
foreach($latest_cache["hits"] as &$keys) {
  ksort($keys);
}

//mbTrace($latest_cache["totals"]);

$smarty = new CSmartyDP();
$smarty->assign("all_layers", Cache::$all_layers);
$smarty->assign("latest_cache", $latest_cache);
$smarty->display("latest_cache_hits.tpl");

