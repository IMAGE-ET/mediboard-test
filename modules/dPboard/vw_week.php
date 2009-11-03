<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleFile("dPboard", "inc_board");

$date = CValue::getOrSession("date", mbDate());
$prec = mbDate("-1 week", $date);
$suiv = mbDate("+1 week", $date);
$vue  = CValue::getOrSession("vue2", CAppUI::pref("AFFCONSULT", 0));

global $smarty;

// Variables de templates
$smarty->assign("date", $date);
$smarty->assign("prec", $prec);
$smarty->assign("suiv", $suiv);
$smarty->assign("vue",  $vue);

$smarty->display("vw_week.tpl");

?>