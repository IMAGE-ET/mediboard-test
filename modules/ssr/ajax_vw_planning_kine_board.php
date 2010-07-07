<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$kine_id = CValue::getOrSession("kine_id");
$date = CValue::getOrSession("date");

$monday = mbDate("last monday", mbDate("+1 day -1 week", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY -1 week", $date));

// Chargement des evenements de la semaine precedente qui n'ont pas encore ete validés
$evenement = new CEvenementSSR();
$where = array();
$where["therapeute_id"] = " = '$kine_id'";
$where["debut"] = " BETWEEN '$monday 00:00:00' AND '$sunday 23:59:59'";
$where["realise"] = " = '0'";
$count_evts = $evenement->countList($where);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("kine_id", $kine_id);
$smarty->assign("count_evts", $count_evts);
$smarty->display("inc_vw_planning_kine_board.tpl");

?>