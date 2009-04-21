<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;
$sejours = mbGetValueFromGet("sejours");

foreach($sejours as $_sejour_id){
  $observation = new CObservationMedicale();
  $observation->sejour_id = $_sejour_id;
  $observation->user_id = $AppUI->user_id;
  $observation->degre = "info";
  $observation->date = mbDateTime();
  $observation->text = "Visite effectuée";
  $msg = $observation->store();
	$AppUI->displayMsg($msg, "CObservationMedicale-msg-create");
}

echo $AppUI->getMsg();
CApp::rip();

?>