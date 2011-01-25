<?php 
/**
 * View interop receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$receiver = new CInteropReceiver(); 

$receivers = array();
foreach (CInteropReceiver::getChildReceivers() as $_interop_receiver) {
  // Récupération de la liste des destinataires
  $itemReceiver = new $_interop_receiver;
  $where = array();
  $receivers[$_interop_receiver] = $itemReceiver->loadList($where);
  if (!is_array($receivers[$_interop_receiver])) {
    continue;
  }
  foreach ($receivers[$_interop_receiver] as $_receiver) {
    $_receiver->loadRefGroup();
    $_receiver->isReachable();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("receiver" , $receiver);
$smarty->assign("receivers", $receivers);
$smarty->display("vw_idx_interop_receivers.tpl");

?>