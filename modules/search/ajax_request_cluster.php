<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();
$request = CValue::get("request");
$type    = CValue::get("type_request");
$content = "";

if ($request && !strripos($request, "delete")) {
  $client = new CHTTPClient($request);

  switch ($type) {
    case "get":
      $content = $client->get();
      break;
    case "put":
      $content = $client->putFile($request);
      break;
    case "post":
      $content = $client->post($request);
      break;
    default: $content = $client->get();
  }
}
$content = json_decode($content, true);
if (!$content) {
  CAppUI::stepAjax("$request est invalide", UI_MSG_ERROR);
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("request", $request);
$smarty->assign("type", $type);
$smarty->assign("content", $content);

$smarty->display("inc_vw_request_cluster.tpl");