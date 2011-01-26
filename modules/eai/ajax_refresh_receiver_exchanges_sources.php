<?php 
/**
 * View interop receiver exchanges sources EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$receiver_guid = CValue::getOrSession("receiver_guid");

$receiver = CMbObject::loadFromGuid($receiver_guid);
$receiver->loadRefsExchangesSources();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("receiver" , $receiver);
$smarty->display("inc_receiver_exchanges_sources.tpl");

?>