<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$echange_hprim_id = CValue::get("echange_hprim_id");
$echange_hprim = new CEchangeHprim;
$echange_hprim->load($echange_hprim_id);
$echange_hprim->loadRefs();

if(CValue::get("message") == 1) {
  $echange = utf8_decode($echange_hprim->_message);
  header("Content-Disposition: attachment; filename=msg-{$echange_hprim->sous_type}-{$echange_hprim_id}.xml");
  header("Content-Type: text/plain; charset=".CApp::$encoding);
  header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
  header( "Cache-Control: post-check=0, pre-check=0", false );
  header("Content-Length: ".strlen($echange));
  echo $echange;
}
if(CValue::get("ack") == 1) {
  $echange = utf8_decode($echange_hprim->_acquittement);
  header("Content-Disposition: attachment; filename=ack-{$echange_hprim->sous_type}-{$echange_hprim_id}.xml");
  header("Content-Type: text/plain; charset=".CApp::$encoding);
  header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
  header( "Cache-Control: post-check=0, pre-check=0", false );
  header("Content-Length: ".strlen($echange));
  echo $echange;
}

?>