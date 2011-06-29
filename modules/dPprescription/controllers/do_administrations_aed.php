<?php /* $Id: do_administration_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

$administrations_ids = explode("_", CValue::post("administrations_ids"));
$decalage = CValue::post("decalage");

if ($decalage > 0) {
  $decalage = "+".$decalage; 
}

foreach($administrations_ids as $administration_id) {
  $administration = new CAdministration;
  $administration->load($administration_id);
  
  $administration->dateTime = mbDateTime($decalage." HOURS", $administration->dateTime);
  if ($msg = $administration->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg(CAppUI::tr("CAdministration-planification-msg-modify"), UI_MSG_OK);
  } 
}

echo CAppUI::getMsg();
CApp::rip();

?>