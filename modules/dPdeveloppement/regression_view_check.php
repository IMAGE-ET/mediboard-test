<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien Ménager
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
