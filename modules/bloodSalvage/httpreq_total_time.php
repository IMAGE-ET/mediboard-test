<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage BloodSalvage
 * @version $Revision: $
 * @author Alexandre Germonneau
 */

global $can, $g;

$can->needsRead();

$date  = mbGetValueFromGetOrSession("date", mbDate());
$blood_salvage_id = mbGetValueFromGetOrSession("blood_salvage_id");
$totaltime = mbGetValueFromGetOrSession("totaltime","00:00:00");

if($blood_salvage_id) {
	$blood_salvage = new CBloodSalvage();
	$blood_salvage->load($blood_salvage_id);
	if($blood_salvage->recuperation_start && $blood_salvage->transfusion_end) {
		$totaltime = mbTimeRelative($blood_salvage->recuperation_start, $blood_salvage->transfusion_end);
	} elseif($blood_salvage->recuperation_start){
		$totaltime = mbTimeRelative($blood_salvage->recuperation_start,mbDateTime());
	}	
	$timeleft = mbTimeRelative($totaltime,"6:00:00");
	if($totaltime > "6:00:00") {
  $timeleft = "00:00:00";
	}
}

$smarty = new CSmartyDP();

$smarty->assign("date"          , $date         );
$smarty->assign("blood_salvage" , $blood_salvage);
$smarty->assign("totaltime"     , $totaltime    );
$smarty->assign("timeleft"      , $timeleft     );

$smarty->display("inc_total_time.tpl");
?>