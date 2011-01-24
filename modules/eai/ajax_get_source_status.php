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

$source_guid = CValue::get("source_guid");

$status = null;

$object = new CMbObject();
$source = $object->loadFromGuid($source_guid);

$source->isReachable();

$status = $source->_reachable;

echo json_encode($status);

?>