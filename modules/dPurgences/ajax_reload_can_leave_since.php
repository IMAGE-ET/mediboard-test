<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$rpus = mbGetValueFromGet("rpus");

foreach($rpus as $_rpu_id) {
	$rpu = new CRPU();
	$rpu->load($_rpu_id);
  
	$listRpu[$_rpu_id]["value"] = ($rpu->_can_leave_since == -1) ? "En consultation" : mbTransformTime($rpu->_can_leave_since, null, "%Hh%M");
	if ($rpu->_can_leave_since == -1) {
		$listRpu[$_rpu_id]["alert"] = "";
  } else if ((CAppUI::conf("dPurgences rpu_warning_time") < $rpu->_can_leave_since) 
	     && ($rpu->_can_leave_since < CAppUI::conf("dPurgences rpu_alert_time"))) {
		$listRpu[$_rpu_id]["alert"] = "warning";
	} else if ($rpu->_can_leave_since > CAppUI::conf("dPurgences rpu_alert_time")) {
		$listRpu[$_rpu_id]["alert"] = "error";
	} else {
		$listRpu[$_rpu_id]["alert"] = "ok";
	}
}

echo json_encode($listRpu);
?>