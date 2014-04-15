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

$start     = CValue::get("start", 0);
$date_min  = CValue::get("date_min", CMbDT::dateTime("-1 WEEK"));
$date_max  = CValue::get("date_max");

$ua = new CUserAgent();
$uas = $ua->loadList(null, "browser_name, browser_version", 100);

CStoredObject::massCountBackRefs($uas, "user_authentications");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("start", $start);
$smarty->assign("user_agents", $uas);

$smarty->display("vw_user_agents.tpl");
