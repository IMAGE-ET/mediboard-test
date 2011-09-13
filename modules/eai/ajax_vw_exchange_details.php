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
$object = new CMbObject();
$exchange = $object->loadFromGuid($exchange_guid);

$exchange->loadRefs(); 
$exchange->loadRefsInteropActor();
$exchange->getErrors();
$exchange->getObservations();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchange", $exchange);

if ($exchange instanceof CExchangeTabular) {
  $smarty->assign("segment_group", $exchange->getMessage());
  $smarty->display("inc_exchange_tabular_details.tpl");
} 
elseif ($exchange instanceof CEchangeXML) {
  $smarty->display("inc_exchange_xml_details.tpl");
}


?>