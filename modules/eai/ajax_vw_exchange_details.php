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

CAppUI::requireLibraryFile("geshi/geshi");

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
      $geshi = new Geshi($msg_segment_group->toXML()->saveXML(), "xml");
      $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
      $geshi->set_overall_style("max-height: 100%; white-space:pre-wrap;");
      $geshi->enable_classes();
      $msg_segment_group->_xml = $geshi->parse_code();
    }
    
    $ack_segment_group = $exchange->getACK();
    if ($ack_segment_group) {
      $geshi = new Geshi($ack_segment_group->toXML()->saveXML(), "xml");
      $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
      $geshi->set_overall_style("max-height: 100%; white-space:pre-wrap;");
      $geshi->enable_classes();
      $ack_segment_group->_xml = $geshi->parse_code();
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
