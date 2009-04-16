<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleFile("bloodSalvage", "inc_personnel");

$blood_salvage_id = mbGetValueFromGetOrSession("blood_salvage_id");
$blood_salvage = new CBloodSalvage();

$date  = mbGetValueFromGetOrSession("date", mbDate());

$modif_operation = $date>=mbDate();

$list_nurse_sspi= CPersonnel::loadListPers("reveil");

$tabAffected = array();
$timingAffect = array();

if($blood_salvage_id) {
	$blood_salvage->load($blood_salvage_id);
	loadAffected($blood_salvage_id, $list_nurse_sspi, $tabAffected, $timingAffect);
}



$smarty = new CSmartyDP();

$smarty->assign("modif_operation",$modif_operation);
$smarty->assign("list_nurse_sspi",$list_nurse_sspi);
$smarty->assign("blood_salvage",$blood_salvage);
$smarty->assign("tabAffected",$tabAffected);
$smarty->assign("timingAffect",$timingAffect);

$smarty->display("inc_vw_blood_salvage_personnel.tpl");
?>