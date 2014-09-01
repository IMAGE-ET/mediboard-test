<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$file = CValue::get("file");
list($module, $view) = explode("/", $file, 2);
list($action, $extention) = explode(".", $view);

$user = CUser::get();
$params["m"] = $module;
$params["raw"] = $action;
$params["info"] = 1;

// Could be done throw session cookie forwarding too
$token = new CViewAccessToken();
$token->_spec->loggable = false;
$token->user_id = $user->_id;
$token->params = CMbString::toQuery($params);
$token->datetime_start = "now";
$token->ttl_hours = 1;
$token->store();

$base = CAppUI::conf("base_url");
$url = "$base/?token={$token->hash}";
$content = file_get_contents($url);

$token->delete();

// Try and get view properties
if (null == $props = json_decode($content)) {
  CAppUI::stepMessage(UI_MSG_ERROR, "regression_checker-noviewinfo");
  return;
}

CAppUI::stepMessage(UI_MSG_OK, "regression_checker-viewinfo-found");

$plan = CView::sampleCheckPlan($props);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("module", $module);
$smarty->assign("action", $action);
$smarty->assign("props", $props);
$smarty->assign("plan", $plan);

$smarty->display("regression_view_check.tpl");
