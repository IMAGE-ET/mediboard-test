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

$config_guid = CValue::get("config_guid");

/**
 * @var CExchangeDataFormatConfig
 */
$config = CMbObject::loadFromGuid($config_guid);

$name = $config->loadRefSender()->_view;

ob_clean();

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=\"config-$name.xml\"");
echo $config->exportXML()->saveXML();

CApp::rip();