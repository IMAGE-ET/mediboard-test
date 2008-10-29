<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsRead();

$date    = mbGetValueFromGetOrSession("date", mbDate());
$bloc_id = mbGetValueFromGetOrSession("bloc_id");

$modif_operation = ($date >= mbDate());
$hour = mbTime();
$blocs_list = CGroups::loadCurrent()->loadBlocs();

$bloc = new CBlocOperatoire();
if(!$bloc->load($bloc_id) && count($blocs_list)) {
	$bloc = reset($blocs_list);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date",            $date);
$smarty->assign("hour",            $hour);
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("blocs_list",      $blocs_list);
$smarty->assign("bloc",            $bloc);

$smarty->display("vw_reveil.tpl");

?>