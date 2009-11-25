<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

// Initialisation de variables

$order_way_pre = CValue::getOrSession("order_way_pre", "ASC");
$order_col_pre = CValue::getOrSession("order_col_pre", "patient_id");
$date          = CValue::getOrSession("date", mbDate());

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date_demain"  , $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"         , $date);
$smarty->assign("order_way_pre", $order_way_pre);
$smarty->assign("order_col_pre", $order_col_pre);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->display("vw_idx_preadmission.tpl");

?>