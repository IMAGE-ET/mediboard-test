<?php 
/**
 * Receive files EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$actor_guid = CValue::get("actor_guid");

$actor = CMbObject::loadFromGuid($actor_guid);
$actor->loadRefGroup();
$actor->loadRefsExchangesSources();

$exchange_source = reset($actor->_ref_exchanges_sources);

$all_files = array();
try {
  $all_files = $exchange_source->receive();
} catch (CMbException $e) {
  $e->stepAjax();
}

$extension = $exchange_source->fileextension;
foreach($all_files as $_file) {
  $filename = basename($_file);
  $message = $exchange_source->getData($_file);
  
  // Dispatch EAI 
  if (!CEAIDispatcher::dispatch($message, $actor)) {
    // création d'un acq en fichier
    //utf8_encode(CEAIDispatcher::$xml_error);
  }

  //$exchange_source->delFile($_file);
}

?>