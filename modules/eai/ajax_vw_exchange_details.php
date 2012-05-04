<?php 
/**
 * View exchange details 
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_guid = CValue::get("exchange_guid");

$observations = $doc_errors_msg = $doc_errors_ack = array();

// Chargement de l'échange demandé
$exchange = CMbObject::loadFromGuid($exchange_guid);

$exchange->loadRefs(); 
$exchange->loadRefsInteropActor();
$exchange->getErrors();
$exchange->getObservations();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchange", $exchange);

switch(true) {
  case $exchange instanceof CExchangeTabular:
    $msg_segment_group = $exchange->getMessage();
    
    if ($msg_segment_group) {
      $msg_segment_group->_xml = CMbString::highlightCode("xml", $msg_segment_group->toXML()->saveXML());
    }
    
    $ack_segment_group = $exchange->getACK();
    if ($ack_segment_group) {
      $ack_segment_group->_xml = CMbString::highlightCode("xml", $ack_segment_group->toXML()->saveXML());
    }
    
    $smarty->assign("msg_segment_group", $msg_segment_group);
    $smarty->assign("ack_segment_group", $ack_segment_group);
    $smarty->display("inc_exchange_tabular_details.tpl");
  break;

  case $exchange instanceof CEchangeXML:
    $smarty->display("inc_exchange_xml_details.tpl");
  break;
  
  default:
    $exchange->guessDataType();
    $smarty->display("inc_exchange_any_details.tpl");
  break;
}
