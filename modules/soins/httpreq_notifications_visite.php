<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user = CUser::get();

$sejours = CValue::get("sejours");

foreach($sejours as $_sejour_id){
  $observation = new CObservationMedicale();
  $observation->sejour_id = $_sejour_id;
  $observation->user_id = $user->_id;
  $observation->degre = "info";
  $observation->date = CMbDT::dateTime();
  $observation->text = "Visite effectuée";
  $msg = $observation->store();
	CAppUI::displayMsg($msg, "CObservationMedicale-msg-create");
}

echo CAppUI::getMsg();
CApp::rip();

?>