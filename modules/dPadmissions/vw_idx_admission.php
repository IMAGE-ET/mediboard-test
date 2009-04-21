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

$selAdmis = mbGetValueFromGetOrSession("selAdmis", "0");
$selSaisis = mbGetValueFromGetOrSession("selSaisis", "0");
$selTri = mbGetValueFromGetOrSession("selTri", "nom");
$order_way = mbGetValueFromGetOrSession("order_way", "ASC");
$order_col = mbGetValueFromGetOrSession("order_col", "_nomPatient");
$date = mbGetValueFromGetOrSession("date", mbDate());
$type = mbGetValueFromGetOrSession("type");

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$sejour = new CSejour();
$sejour->_type_admission = $type;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("date_demain", $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"     , $date);
$smarty->assign("selAdmis" , $selAdmis);
$smarty->assign("selSaisis", $selSaisis);
$smarty->assign("selTri"   , $selTri);
$smarty->assign("order_way"   , $order_way);
$smarty->assign("order_col"   , $order_col);
$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);

$smarty->display("vw_idx_admission.tpl");

?>