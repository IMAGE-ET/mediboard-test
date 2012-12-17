<?php 
/**
 * Export CExchangeDataFormatConfig
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object_guid = CValue::get("object_guid");

/**
 * @var CExchangeDataFormatConfig
 */
$object = CMbObject::loadFromGuid($object_guid);

ob_clean();

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=\"config.xml\"");
echo $object->exportXMLConfigValues()->saveXML();
  
CApp::rip();