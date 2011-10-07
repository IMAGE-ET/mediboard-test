<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10762 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::post("prescription_id");
$lines           = CValue::post("lines");

foreach($lines as $guid => $selected_line) {
  if ($selected_line) {
    list($line_class, $line_id) = explode("-", $guid);
    $line = new $line_class;
		$line->load($line_id);
		
    if($line instanceof CPrescriptionLineMix){
    	if($line->_date_fin > mbDateTime()){
    		if($line->unite_duree == "heure"){
    			$_duree = mbHoursRelative(mbDateTime(), $line->_date_fin);
    		}
				if($line->unite_duree == "jour"){
          $_duree = mbDaysRelative(mbDate(), mbDate($line->_date_fin));
        }
				
    		// mise a jour de la duree
	      $line->duplicatePerf(mbDate(), mbTime(), $_duree);
	      $line->store();
			}
    } elseif ($line instanceof CPrescriptionLineMedicament) {
    	if($line->_old_fin_reelle > mbDateTime()){
    		$_duree = mbDaysRelative(mbDate(), mbDate($line->_old_fin_reelle));
        $line->duplicateLine(CAppUI::$user->_id, $prescription_id, mbDate(), mbTime(), $_duree);
    	}
		} else {
    	$line->date_arret = "";
			$line->time_arret = "";
			$line->store();
    }
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>