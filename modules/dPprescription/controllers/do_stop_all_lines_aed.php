<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10762 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::post("prescription_id");
$lines           = CValue::post("lines", array());

foreach($lines as $guid => $selected_line) {
  if ($selected_line) {
    list($line_class, $line_id) = explode("-", $guid);
    $line = new $line_class;
    $line->load($line_id);
    
    $line->date_arret = mbDate();
    $line->time_arret = mbTime();
    $line->store();
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>