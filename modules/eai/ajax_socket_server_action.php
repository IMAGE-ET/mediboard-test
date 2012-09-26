<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage eai
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$port   = CValue::get("port");
$type   = CValue::get("type");
$action = CValue::get("action");
$uid    = CValue::get("uid");

if (!$port) {
  CAppUI::displayAjaxMsg("No port specified", UI_MSG_ERROR);
}
    
switch ($action) {
  case "stop" :
  case "restart": 
    try {
      CSocketBasedServer::send("localhost", $port, "__".strtoupper($action)."__\n");
      CAppUI::displayAjaxMsg("Serveur $type : '$action' ");
    }
    catch(Exception $e) {
      CAppUI::displayAjaxMsg($e->getMessage(), UI_MSG_ERROR);
    }
    break;
    
   case "test" : 
    try {
      $server_class = '';
      switch ($type) {
        case "Dicom" :
          $server_class = "CDicomServer";
          break;
        case "MLLP" :
          $server_class = "CMLLPServer";
          break;
        default :
          return;
      }
      $response = CSocketBasedServer::send("localhost", $port, $server_class::sampleMessage()); 
      echo "<pre class='er7'>$response</pre>";
      return;
    }
    catch(Exception $e) {
      CAppUI::displayAjaxMsg($e->getMessage(), UI_MSG_ERROR);
    }
    break;
    
  case "stats": 
    try {
      mbTrace(json_decode(CSocketBasedServer::send("localhost", $port, "__".strtoupper($action)."__\n"), true));
    }
    catch(Exception $e) {
      CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
    }
    return;
    
  default:
    CAppUI::displayAjaxMsg("Unknown command '$action'", UI_MSG_ERROR);
}

$processes = CSocketBasedServer::getPsStatus();
$process_id = CValue::get("process_id");

if (!array_key_exists($process_id, $processes)) {
  return;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("process_id", $process_id);
$smarty->assign("uid", $uid);
$smarty->assign("_process", $processes[$process_id]);
$smarty->display("inc_server_socket.tpl"); 

?>