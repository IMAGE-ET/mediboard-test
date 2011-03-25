<?php 
/**
 * Messages supported
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid          = CValue::getOrSession("actor_guid");
$exchange_class_name = CValue::getOrSession("exchange_class_name");

$data_format = new $exchange_class_name;
$messages = $data_format->getMessagesSupported($actor_guid);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("messages"    , $messages);
$smarty->display("inc_messages_supported.tpl");

?>