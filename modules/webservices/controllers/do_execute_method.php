<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$method               = CValue::post("func");
$exchange_source_guid = CValue::post("exchange_source_guid");
$parameters           = CValue::post("parameters");

$exchange_source = CMbObject::loadFromGuid($exchange_source_guid);
$exchange_source->setData($parameters);
$exchange_source->send($method, true);

echo $exchange_source->getACQ();

CApp::rip();

?>