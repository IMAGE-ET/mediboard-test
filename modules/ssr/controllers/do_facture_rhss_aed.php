<?php /* $Id: do_rpu_aed.php 6473 2009-06-24 15:18:19Z lryo $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$rhs_ids = CValue::post("rhs_ids");

foreach($rhs_ids as $_rhs_id) {
  $rhs = new CRHS();
  $rhs->load($_rhs_id);
  // Passage à facturer
  $rhs->facture = 1;
  $msg = $rhs->store();
  CAppUI::displayMsg($msg, "CRHS-msg-modify"); 
}

echo CAppUI::getMsg();

CApp::rip();

?>