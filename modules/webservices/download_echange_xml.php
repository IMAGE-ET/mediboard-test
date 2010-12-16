<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$echange_xml_guid = CValue::get("echange_xml_guid");
$echange_xml = CMbObject::loadFromGuid($echange_xml_guid);
$echange_xml->loadRefs();

if(CValue::get("message") == 1) {
  $echange = utf8_decode($echange_xml->_message);
  header("Content-Disposition: attachment; filename=msg-{$echange_xml->sous_type}-{$echange_xml->_id}.xml");
  header("Content-Type: text/plain; charset=".CApp::$encoding);
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
  header("Cache-Control: post-check=0, pre-check=0", false );
  header("Content-Length: ".strlen($echange));
  echo $echange;
}
if(CValue::get("ack") == 1) {
  $echange = utf8_decode($echange_xml->_acquittement);
  header("Content-Disposition: attachment; filename=ack-{$echange_xml->sous_type}-{$echange_xml->_id}.xml");
  header("Content-Type: text/plain; charset=".CApp::$encoding);
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
  header("Cache-Control: post-check=0, pre-check=0", false );
  header("Content-Length: ".strlen($echange));
  echo $echange;
}

?>