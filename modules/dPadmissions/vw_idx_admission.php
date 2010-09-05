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

// Initialisation de variables

$selAdmis = CValue::getOrSession("selAdmis", "0");
$selSaisis = CValue::getOrSession("selSaisis", "0");
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "patient_id");
$date = CValue::getOrSession("date", mbDate());
$type = CValue::getOrSession("type", "0");

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$sejour = new CSejour();
$sejour->_type_admission = $type;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"       , $sejour);
$smarty->assign("date_demain"  , $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"         , $date);
$smarty->assign("selAdmis"     , $selAdmis);
$smarty->assign("selSaisis"    , $selSaisis);
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->display("vw_idx_admission.tpl");

?>