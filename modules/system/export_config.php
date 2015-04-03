<?php
/**
 * Export CExchangeDataFormatConfig
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$object_guid = CValue::get("object_guid");

/** @var CExchangeDataFormatConfig $object */
$object = CMbObject::loadFromGuid($object_guid);

$name = $object->loadRefObject()->_view;

ob_clean();

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=\"config-$name.xml\"");
echo $object->exportXMLConfigValues()->saveXML();
  
CApp::rip();