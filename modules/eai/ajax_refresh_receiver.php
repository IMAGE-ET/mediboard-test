<?php 
/**
 * Details interop receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$receiver_guid       = CValue::getOrSession("receiver_guid");
$receiver_class_name = CValue::getOrSession("receiver_class_name");

// Chargement du destinataire d'interopérabilité
if ($receiver_class_name) {
  $receiver = new $receiver_class_name;
} else {
  if ($receiver_guid) {
    $receiver = CMbObject::loadFromGuid($receiver_guid);
    if ($receiver->_id) {
      $receiver->loadRefGroup();
      $receiver->loadRefsExchangesSources();
    }
  } else {
   $receiver = new CInteropReceiver(); 
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("receiver" , $receiver);
$smarty->display("inc_receiver.tpl");

?>