<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleFile("dPboard", "inc_board");

$date = mbGetValueFromGetOrSession("date", mbDate());
$prec = mbDate("-1 day", $date);
$suiv = mbDate("+1 day", $date);
$vue  = mbGetValueFromGetOrSession("vue2", CAppUI::pref("AFFCONSULT", 0));

global $smarty;

// Variables de templates
$smarty->assign("date", $date);
$smarty->assign("prec", $prec);
$smarty->assign("suiv", $suiv);
$smarty->assign("vue",  $vue);

$smarty->display("vw_day.tpl");

?>