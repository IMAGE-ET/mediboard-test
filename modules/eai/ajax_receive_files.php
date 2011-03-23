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
} catch (Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
}

$extension = $exchange_source->fileextension;
foreach($all_files as $_file) {
  $filename = basename($_file);
  $file = $exchange_source->getData($_file);
  
  try {
    CEAIDispatcher::dispatch($file);
  } catch(Exception $e) {
    CAppUI::stepAjax($e->getMessage());
    // création d'un échange Any
  }
  

  //$exchange_source->delFile($_file);
}

?>