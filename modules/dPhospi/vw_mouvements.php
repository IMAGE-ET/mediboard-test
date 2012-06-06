<?php /* $Id: vw_mouvements.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ini_set("memory_limit", "256M");

$services_ids = CValue::getOrSession("services_ids");
$granularite  = CValue::getOrSession("granularite", "day");
$date         = CValue::getOrSession("date", mbDate());
$granularites = array("day", "week", "4weeks");
$triAdm       = CValue::getOrSession("triAdm", "praticien");
$mode_vue_tempo = CValue::getOrSession("mode_vue_tempo", "classique");
$readonly     = CValue::get("readonly");
$prestation_id = CValue::getOrSession("prestation_id", CAppUI::pref("prestation_id_hospi"));

// Si c'est la préférence utilisateur, il faut la mettre en session 
CValue::setSession("prestation_id", $prestation_id);

$prestation_journaliere = new CPrestationJournaliere;

$prestations_journalieres = $prestation_journaliere->loadList();

$smarty = new CSmartyDP;

$smarty->assign("date"        , $date);
$smarty->assign("granularites", $granularites);
$smarty->assign("granularite" , $granularite);
$smarty->assign("mode_vue_tempo", $mode_vue_tempo);
$smarty->assign("prestations_journalieres", $prestations_journalieres);
$smarty->assign("prestation_id", $prestation_id);

$smarty->display("vw_mouvements.tpl");
?>