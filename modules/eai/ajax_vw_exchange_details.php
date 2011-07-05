<?php 
/**
 * View details exchange
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
$smarty->display("inc_exchange_details.tpl");

?>