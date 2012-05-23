<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$port   = CValue::get("port");
$action = CValue::get("action");
$uid    = CValue::get("uid");

if (!$port) {
  CAppUI::displayAjaxMsg("No port specified", UI_MSG_ERROR);
}
    
switch ($action) {
  case "stop" :
  case "restart": 
    try {
      CMLLPServer::send("localhost", $port, "__".strtoupper($action)."__\n");
      CAppUI::displayAjaxMsg("Serveur MLLP : '$action' ");
    }
    catch(Exception $e) {
      CAppUI::displayAjaxMsg($e->getMessage(), UI_MSG_ERROR);
    }
    break;
    
   case "test" : 
    try {
      $response = CMLLPServer::send("localhost", $port, "\x0B".CMLLPServer::ORU()."\x1C\x0D"); 
      echo "<pre class='er7'>$response</pre>";
    }
    catch(Exception $e) {
      CAppUI::displayAjaxMsg($e->getMessage(), UI_MSG_ERROR);
    }
    break;
    
  case "stats": 
    try {
      CMLLPServer::send("localhost", $port, "yh\n");
      mbTrace(json_decode(CMLLPServer::send("localhost", $port, "__".strtoupper($action)."__\n"), true));
    }
    catch(Exception $e) {
      CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
    }
    return;
    
  default:
    CAppUI::displayAjaxMsg("Unknown command '$action'", UI_MSG_ERROR);
}

$processes = CMLLPServer::get_ps_status();
$process_id = CValue::get("process_id");

if (!array_key_exists($process_id, $processes)) {
  return;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("process_id", $process_id);
$smarty->assign("uid", $uid);
$smarty->assign("_process", $processes[$process_id]);
$smarty->display("inc_server_mllp.tpl"); 

?>