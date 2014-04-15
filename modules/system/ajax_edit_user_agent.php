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

CCanDo::checkAdmin();

$user_agent_id = CValue::get("user_agent_id");

$ua = new CUserAgent();
$ua->load($user_agent_id);
$ua->loadRefsNotes();

$ua->countBackRefs("user_authentications");

$detect = CUserAgent::detect($ua->user_agent_string);

$smarty = new CSmartyDP();
$smarty->assign("ua", $ua);
$smarty->assign("detect", $detect);
$smarty->display("inc_edit_user_agent.tpl");
