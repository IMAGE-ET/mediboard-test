<?php 
/**
 * Status source interop receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$source_guid = CValue::get("source_guid");

$status = null;

$object = new CMbObject();
$source = $object->loadFromGuid($source_guid);

$source->isReachable();

$status = array ("reachable" => $source->_reachable,
                 "message"   => utf8_encode($source->_message),
                 "name"      => $source->name
          );

echo json_encode($status);

?>