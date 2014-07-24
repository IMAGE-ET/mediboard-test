<?php 
/**
 * View interop actors EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$receiver  = new CInteropReceiver();
$receivers = array();

$sender  = new CInteropSender();
$senders = array();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("receiver" , $receiver);
$smarty->assign("receivers", $receivers);

$smarty->assign("sender" , $sender);
$smarty->assign("senders", $senders);

$smarty->display("vw_idx_interop_actors.tpl");

