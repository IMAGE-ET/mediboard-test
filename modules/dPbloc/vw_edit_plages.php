<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $m;
require_once("modules/dPbloc/checkDate.php");
require_once($AppUI->getModuleClass($m, "planning"));
require_once($AppUI->getModuleClass($m, "plagesop"));
require_once($AppUI->getModuleClass($m, "salle"));
require_once($AppUI->getModuleClass("mediusers"));

$date = mbGetValueFromGetOrSession("date", mbDate());

$dateParts = explode("-", $date);
$year  = $dateParts[0];
$month = $dateParts[1];
$day   = $dateParts[2];

$planning = new Cplanning($day, $month, $year);
?>

<script language="javascript" type="text/javascript">
function popPlanning(debut) {
  var url = new Url;
  url.setModuleAction("dPbloc", "view_planning");
  url.addParam("deb", debut);
  url.addParam("fin", debut);
  url.popup(700, 550, "Planning");
}
</script>

<table class="main">
	<tr>
		<td class="greedyPane">
<?php
$planning->displayJour();

////////////////// NEW WAY /////////////////////
global $m, $AppUI;

$plagesel = new CPlageOp;
$plagesel->load(mbGetValueFromGetOrSession("id"));

$salle = new CSalle;
$salles = $salle->loadlist();

$function = new CFunctions;
$specs = $function->loadSpecialites();

$mediuser = new CMediusers;
$chirs = $mediuser->loadChirurgiens();
$anesths = $mediuser->loadAnesthesistes();

// Heures & minutes
$start = 8;
$stop = 20;
$step = 15;

for ($i = $start; $i < $stop; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $step) {
    $mins[] = $i;
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign('plagesel', $plagesel);
$smarty->assign('chirs', $chirs);
$smarty->assign('anesths', $anesths);
$smarty->assign('salles', $salles);
$smarty->assign('specs', $specs);

$smarty->assign('heures', $hours);
$smarty->assign('minutes', $mins);

$smarty->assign('date'  , $date);
$smarty->assign('day'  , $day);
$smarty->assign('month', $month);
$smarty->assign('year' , $year);

$smarty->display('vw_edit_plages.tpl');
?>

    </td>
  </tr>
</table>