<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;
$ds = CSQLDataSource::get("std");

$can->needsRead();

// Initialisation de variables
$date = CValue::getOrSession("date", mbDate());
$month = mbTransformTime("+ 0 day", $date, "%Y-%m-__");
$lastmonth = mbDate("-1 month", $date);
$nextmonth = mbDate("+1 month", $date);

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);// Récupération de la liste des anesthésistes
$mediuser = new CMediusers;
$anesthesistes = $mediuser->loadAnesthesistes(PERM_READ);

$consult = new CConsultation();

// Récupération du nombre de consultations par jour
$ljoin = array();
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where = array();
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($anesthesistes));
$where["plageconsult.date"] = "like '$month'";
$order = "plageconsult.date";
$groupby = "plageconsult.date";

$fields = array("plageconsult.date");

$listMonth = $consult->countMultipleList($where, $order, null, $groupby, $ljoin, $fields);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"     , $hier);
$smarty->assign("demain"   , $demain);
$smarty->assign("date"     , $date);
$smarty->assign("lastmonth", $lastmonth);
$smarty->assign("nextmonth", $nextmonth);
$smarty->assign("listMonth", $listMonth);

$smarty->display('inc_vw_all_preadmissions.tpl');

?>