<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

// Type d'affichage
$vue = CValue::getOrSession("vue", 0);
$filter_function_id = CValue::getOrSession("filter_function_id");

// Récupération des dates
$date = CValue::getOrSession("date", mbDate());
$order_col = CValue::getOrSession("order_col","_nomPatient");
$order_way = CValue::getOrSession("order_way","ASC");

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$now  = mbDate();

// Chargement des functions
$function = new CFunctions();
$functions = $function->loadSpecialites(PERM_READ);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date_demain", $date_demain);
$smarty->assign("date" , $date );
$smarty->assign("now" , $now );
$smarty->assign("vue" , $vue );
$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);
$smarty->assign("functions", $functions);
$smarty->assign("filter_function_id", $filter_function_id);
$smarty->display("vw_idx_sortie.tpl");

?>