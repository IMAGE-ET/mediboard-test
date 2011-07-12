<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$day = 60*60*24;
$monday = 4*$day;

$list_days = array();
$list_days_schedule = array();
$conf = array_flip(str_split(CAppUI::conf('pharmacie dispensation_schedule')));

// Liste des jours de la semaine et le planning de dispensation
for($i = 0; $i < 7; $i++) {
	$list_days[$i] = strftime('%A', $monday+$i*$day);
	$list_days_schedule[$i] = array_key_exists($i, $conf);
}

$list_hours = range(1, 24);
foreach($list_hours as &$_hour){
  $_hour = str_pad($_hour,2,"0",STR_PAD_LEFT);
}
$list_hours[] = "";
sort($list_hours);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('list_days', $list_days);
$smarty->assign('list_days_schedule', $list_days_schedule);
$smarty->assign("list_hours", $list_hours);
$smarty->display("configure.tpl");

?>