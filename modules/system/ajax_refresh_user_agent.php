<?php
/**
 * $Id: ajax_graph_user_agents.php 24464 2014-08-19 08:59:53Z kgrisel $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 24464 $
 */

CCanDo::checkRead();

$user_agent_id = CValue::get("user_agent_id");

$ua   = new CUserAgent();
if ($user_agent_id) {
  $ua->load($user_agent_id);

  if ($ua->_id) {
    $ua->countBackRefs("user_authentications");
  }
}

$smarty = new CSmartyDP();
$smarty->assign("_user_agent", $ua);
$smarty->display("inc_vw_user_agents_line.tpl");