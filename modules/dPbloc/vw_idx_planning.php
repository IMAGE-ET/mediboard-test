<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

include_once("modules/dPbloc/checkDate.php");
require_once($AppUI->getModuleClass('dPbloc', 'planning'));
require_once($AppUI->getModuleClass('mediusers', 'functions'));

$date = mbGetValueFromGetOrSession("date", mbDate());

$dateParts = explode("-", $date);
$year  = $dateParts[0];
$month = $dateParts[1];
$day   = $dateParts[2];

$planning = new Cplanning($day, $month, $year);
?>

<table class="main">
	<tr>
		<td class="greedyPane" rowspan="2"><?php $planning->displaySem(); ?></td>
	</tr>
	<tr>
		<td>
<?php
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('date', $date);
$smarty->assign('listSpec', $listSpec);

$smarty->display('vw_idx_planning.tpl');

?>

		</td>
	</tr>
</table>