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

$data = array ("reachable" => $source->_reachable,
               "errors"    => utf8_encode($source->_errors));

echo json_encode($data);

?>