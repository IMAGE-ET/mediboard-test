<?php 
/**
 * Download exchange XML
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$exchange_guid = CValue::get("exchange_guid");
$exchange_object = CMbObject::loadFromGuid($exchange_guid);
$exchange_object->loadRefs();

$extension = ".txt";
if ($exchange_object instanceof CEchangeXML) {
  $extension = ".xml";    
}
if ($exchange_object instanceof CExchangeIHE) {
  $extension = ".HL7";    
}

if (CValue::get("message") == 1) {
  $exchange = utf8_decode($exchange_object->_message);
  header("Content-Disposition: attachment; filename=msg-{$exchange_object->sous_type}-{$exchange_object->_id}{$extension}");
  header("Content-Type: text/plain; charset=".CApp::$encoding);
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
  header("Cache-Control: post-check=0, pre-check=0", false );
  header("Content-Length: ".strlen($exchange));
  echo $exchange;
}
if (CValue::get("ack") == 1) {
  $exchange = utf8_decode($exchange_object->_acquittement);
  header("Content-Disposition: attachment; filename=ack-{$exchange_object->sous_type}-{$exchange_object->_id}{$extension}");
  header("Content-Type: text/plain; charset=".CApp::$encoding);
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
  header("Cache-Control: post-check=0, pre-check=0", false );
  header("Content-Length: ".strlen($exchange));
  echo $exchange;
}

?>