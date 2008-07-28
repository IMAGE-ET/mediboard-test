<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;
$hour = mbTime(null);


// Création du template
$smarty = new CSmartyDP();


$smarty->assign("date"          , $date        );
$smarty->assign("hour"          , $hour        );
$smarty->assign("modif_operation", $modif_operation);
$smarty->display("vw_reveil.tpl");

?>