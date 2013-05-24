<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage eai
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$process_id = CValue::get("process_id");
$uid        = CValue::get("uid");

$tmp_dir    = CSocketBasedServer::getTmpDir();
$pid_files  = glob("$tmp_dir/pid.*");

foreach($pid_files as $_file) {
  $_pid = substr($_file, strrpos($_file, ".") + 1);
  if ($process_id != $_pid) {
    continue;
  }
  
  if (@unlink($_file) === true) {
    CAppUI::displayAjaxMsg("Le fichier 'pid.$process_id' a été supprimé");
    return;
  } 
}

CAppUI::displayAjaxMsg("Le fichier 'pid.$process_id' n'a pas pu être supprimé", UI_MSG_ERROR);

$processes = CSocketBasedServer::getPsStatus();
if (!array_key_exists($process_id, $processes)) {
  return;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("process_id", $process_id);
$smarty->assign("uid"       , $uid);
$smarty->assign("_process"  , $processes[$process_id]);
$smarty->display("inc_server_socket.tpl"); 
    

