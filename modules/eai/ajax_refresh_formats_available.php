<?php 
/**
 * Formats available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid = CValue::getOrSession("actor_guid");

$formats_xml     = CExchangeDataFormat::getAll("CEchangeXML");
$formats_tabular = CExchangeDataFormat::getAll("CExchangeTabular");

$messages_xml = array();
foreach ($formats_xml as $_format_xml) {
  $data_format = new $_format_xml;
  $temp = $data_format->getMessagesSupported($actor_guid, false);
  $messages_xml = array_merge($messages_xml, $temp);
}

$messages_tabular = array();
foreach ($formats_tabular as $_format_tabular) {
  $data_format = new $_format_tabular;
  $temp = $data_format->getMessagesSupported($actor_guid, false);
  $messages_tabular = array_merge($messages_tabular, $temp);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("actor_guid"      , $actor_guid);
$smarty->assign("formats_xml"     , $formats_xml);
$smarty->assign("messages_xml"    , $messages_xml);
$smarty->assign("formats_tabular" , $formats_tabular);
$smarty->assign("messages_tabular", $messages_tabular);
$smarty->display("inc_formats_available.tpl");

?>