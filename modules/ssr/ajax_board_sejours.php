<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date    = CValue::getOrSession("date", mbDate());
$kine_id = CValue::getOrSession("kine_id");

$planning = new CPlanningWeek($date);

// Sejour SSR
$sejour = new CSejour;

// Sejours pour lequel le kine est référent
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$join["bilan_ssr"]  = "bilan_ssr.sejour_id = sejour.sejour_id";
$join["technicien"] = "technicien.technicien_id = bilan_ssr.technicien_id";
$where["technicien.kine_id"] = "= '$kine_id'";
$order = "sejour.entree, sejour.sortie";
$sejours["referenced"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lequel le kine est remplaçant
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$join["replacement"]  = "replacement.sejour_id = sejour.sejour_id";
$where["replacement.replacement_id"] = "IS NOT NULL";
$where["replacement.replacer_id"] = " = '$kine_id'";
$order = "sejour.entree, sejour.sortie";
$sejours["replaced"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lequel le kine a des événements
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";

$join["evenement"]  = "replacement.sejour_id = sejour.sejour_id";
$where["replacement.replacement_id"] = "IS NOT NULL";
$where["replacement.replacer_id"] = " = '$kine_id'";
$order = "sejour.entree, sejour.sortie";

//$sejours["planned"] = $sejour->loadList($where, null, null, null, $join);

//$sejours["plannable"] = array();

foreach ($sejours as &$_sejours) {
	foreach ($_sejours as $_sejour) {
		$_sejour->loadRefPatient();
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->display("inc_board_sejours.tpl");

?>