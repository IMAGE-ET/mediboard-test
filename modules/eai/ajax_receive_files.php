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

$exchange_source->receive();

?>