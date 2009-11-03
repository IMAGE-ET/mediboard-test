<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;

$can->needsRead();

$blood_salvage_id = CValue::get("blood_salvage_id");
$totaltime = CValue::getOrSession("totaltime","00:00:00");
$blood_salvage = new CBloodSalvage();
$timeleft = "06:00:00";
if ($blood_salvage_id) {
	$blood_salvage->load($blood_salvage_id);
	$blood_salvage->loadRefPlageOp();
	
	if ($blood_salvage->recuperation_start && $blood_salvage->transfusion_end) {
		$totaltime = mbTimeRelative($blood_salvage->recuperation_start, $blood_salvage->transfusion_end);
	} 
	elseif ($blood_salvage->recuperation_start){
		$totaltime = mbTimeRelative($blood_salvage->recuperation_start,mbDate($blood_salvage->_datetime)." ".mbTime());
	}	
	$timeleft = mbTimeRelative($totaltime,"06:00:00");
	if ($totaltime > "06:00:00") {
    $timeleft = "00:00:00";
	}
}

$smarty = new CSmartyDP();

$smarty->assign("blood_salvage" , $blood_salvage);
$smarty->assign("totaltime"     , $totaltime    );
$smarty->assign("timeleft"      , $timeleft     );

$smarty->display("inc_total_time.tpl");
?>